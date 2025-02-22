<?php
// src/Controller/BackofficeController.php
namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductSize;
use App\Form\ProductType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Doctrine\ORM\EntityManagerInterface;

class BackofficeController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/backoffice', name: 'app_backoffice', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        // Création d'un nouveau produit pour l'ajout
        $product = new Product();
        $sizes = ['XS', 'S', 'M', 'L', 'XL'];
        foreach ($sizes as $size) {
            $productSize = new ProductSize();
            $productSize->setSize($size);
            $productSize->setQuantity(0);  // initialiser à 0 pour chaque taille
            $product->addSizeStock($productSize);
        }

        // Création du formulaire d'ajout
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        // Récupérer tous les produits existants
        $products = $this->entityManager->getRepository(Product::class)->findAll();

        // Générer un formulaire de modification pour chaque produit existant
        $editForms = [];
        foreach ($products as $productToEdit) {
            $editForms[$productToEdit->getId()] = $this->createForm(ProductType::class, $productToEdit, [
                'action' => $this->generateUrl('app_backoffice_edit', ['id' => $productToEdit->getId()]),
                'method' => 'POST',
            ])->createView();
        }

        // Si le formulaire d'ajout est soumis et valide, on gère l'ajout
        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleProductForm($form, $product); // Assure-toi que cette méthode gère correctement le produit
            $this->addFlash('success', 'Produit ajouté avec succès!');
            return $this->redirectToRoute('app_backoffice');
        }

        // Retourner à la vue avec les produits existants et le formulaire d'ajout
        return $this->render('backoffice/index.html.twig', [
            'form' => $form->createView(),
            'products' => $products,
            'editForms' => $editForms,
        ]);
    }



    #[Route('/backoffice/edit/{id}', name: 'app_backoffice_edit', methods: ['POST'])]
    public function edit(Product $product, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleProductForm($form, $product);
            
            // Enregistrer les modifications dans la base de données
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Produit modifié avec succès!');
        } else {
            $this->addFlash('error', 'Erreur lors de la modification du produit.');
        }

        return $this->redirectToRoute('app_backoffice');
    }


    #[Route('/backoffice/delete/{id}', name: 'app_backoffice_delete', methods: ['POST'])]
    public function delete(Product $product, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            // Suppression du produit
            $this->entityManager->remove($product);
            $this->entityManager->flush();
            
            $this->addFlash('success', 'Produit supprimé avec succès!');
        } else {
            $this->addFlash('error', 'Erreur lors de la suppression du produit.');
        }

        return $this->redirectToRoute('app_backoffice');
    }



    private function handleProductForm($form, Product $product): void
    {
        $imageFile = $form->get('imageFilename')->getData();
        if ($imageFile) {
            $newFilename = uniqid() . '.' . $imageFile->guessExtension();
            try {
                $imageFile->move($this->getParameter('images_directory'), $newFilename);
            } catch (FileException $e) {
                $this->addFlash('error', "Erreur lors du téléchargement de l image.");
                return;
            }
            $product->setImageFilename($newFilename);
        }
        
        foreach ($product->getSizeStocks() as $productSize) {
            $productSize->setProduct($product);
            $this->entityManager->persist($productSize);
        }

        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }
}
