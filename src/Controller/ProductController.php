<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;


final class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(ProductRepository $productRepository, Request $request): Response
    {
        $priceRange = $request->query->get('price_range', 'all');

        $priceRanges = [
            '10-29' => [10, 29],
            '30-35' => [29, 35],
            '35-50' => [35, 50],
        ];

        // Création de la requête avec QueryBuilder
        $queryBuilder = $productRepository->createQueryBuilder('p');

        if (isset($priceRanges[$priceRange])) {
            [$minPrice, $maxPrice] = $priceRanges[$priceRange];
            $queryBuilder->andWhere('p.price >= :minPrice')
                        ->andWhere('p.price <= :maxPrice')
                        ->setParameter('minPrice', $minPrice)
                        ->setParameter('maxPrice', $maxPrice);
        }

        $queryBuilder->orderBy('p.price', 'ASC');
        $products = $queryBuilder->getQuery()->getResult();

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'selectedRange' => $priceRange,
        ]);
    }

    #[Route('/product/{id}', name: 'product_show')]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product, // Envoie un produit spécifique à la vue
        ]);
    }
}

