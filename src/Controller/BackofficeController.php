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

    #[Route('/backoffice', name: 'app_backoffice')]
    public function index(Request $request): Response
    {
        $product = new Product();

        // Créer les tailles par défaut (xs, s, m, l, xl)
        $sizes = ['XS', 'S', 'M', 'L', 'XL'];
        foreach ($sizes as $size) {
            $productSize = new ProductSize();
            $productSize->setSize($size);
            $productSize->setQuantity(0);  // Quantité initiale
            $product->addSizeStock($productSize);  // Ajouter une taille au produit
        }

        // Créer le formulaire pour le produit
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        // Charger tous les produits
        $products = $this->entityManager->getRepository(Product::class)->findAll();

        // Tableau pour les formulaires d'édition
        $editForms = [];

        // Création des formulaires pour chaque produit existant
        foreach ($products as $existingProduct) {
            $editForms[$existingProduct->getId()] = $this->createForm(ProductType::class, $existingProduct);
            $editForms[$existingProduct->getId()]->handleRequest($request);

            // Si le formulaire de modification est soumis et valide, on met à jour le produit
            if ($editForms[$existingProduct->getId()]->isSubmitted() && $editForms[$existingProduct->getId()]->isValid()) {
                // Traitement de l'image pour le produit existant
                $imageFile = $editForms[$existingProduct->getId()]->get('imageFilename')->getData();
                if ($imageFile) {
                    $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                    try {
                        $imageFile->move($this->getParameter('images_directory'), $newFilename);
                    } catch (FileException $e) {
                        $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image.');
                        return $this->redirectToRoute('app_backoffice');
                    }
                    $existingProduct->setImageFilename($newFilename);
                }

                // Sauvegarder les stocks de tailles pour le produit modifié
                foreach ($existingProduct->getSizeStocks() as $productSize) {
                    $productSize->setProduct($existingProduct);
                    $this->entityManager->persist($productSize);
                }

                $this->entityManager->flush();
                $this->addFlash('success', 'Produit modifié avec succès!');
                return $this->redirectToRoute('app_backoffice');
            }
        }

        // Gérer la soumission du formulaire de création de produit
        if ($form->isSubmitted() && $form->isValid()) {
            // Traitement de l'image et sauvegarde du produit
            $imageFile = $form->get('imageFilename')->getData();
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move($this->getParameter('images_directory'), $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image.');
                    return $this->redirectToRoute('app_backoffice');
                }
                $product->setImageFilename($newFilename);
            }

            // Sauvegarder les stocks de tailles
            foreach ($product->getSizeStocks() as $productSize) {
                $productSize->setProduct($product);
                $this->entityManager->persist($productSize);
            }

            $this->entityManager->persist($product);
            $this->entityManager->flush();

            $this->addFlash('success', 'Produit ajouté avec succès!');
            return $this->redirectToRoute('app_backoffice');
        }

        return $this->render('backoffice/index.html.twig', [
            'form' => $form->createView(),  // Formulaire pour créer un produit
            'products' => $products,        // Liste des produits
            'editForms' => array_map(fn($form) => $form->createView(), $editForms), // Formulaires pour modifier les produits
        ]);
    }


   



    #[Route('/backoffice/delete/{id}', name: 'app_backoffice_delete', methods: ['POST'])]
    public function delete(Product $product, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($product);
            $this->entityManager->flush();
            $this->addFlash('success', 'Produit supprimé avec succès!');
        }

        return $this->redirectToRoute('app_backoffice');
    }


}