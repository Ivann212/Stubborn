<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\StripeService as ServiceStripeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use App\Service\StripeService;
use Stripe\Checkout\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CartController extends AbstractController
{
    public function __construct(private readonly ProductRepository $productRepository)
    {
    }

    #[Route('/cart', name: 'cart')]
    public function index(SessionInterface $session, ParameterBagInterface $params): Response
    {
        $cart = $session->get('cart', []);

        $cartWithData = [];
        $total = 0;

        foreach ($cart as $id => $quantity) {
            $product = $this->productRepository->find($id);
            if ($product) {
                $cartWithData[] = [
                    'product' => $product,
                    'quantity' => $quantity
                ];
                $total += $product->getPrice() * $quantity;
            }
        }

        return $this->render('cart/index.html.twig', [
            'cart' => $cartWithData,
            'stripe_public_key' => $params->get('stripe_public_key'),
            'total' => $total,
        ]);
    }

    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function addToCart(int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);

        $cart[$id] = ($cart[$id] ?? 0) + 1;

        $session->set('cart', $cart);

        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function removeFromCart(int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []);
        
        if (isset($cart[$id])) {
            unset($cart[$id]);
            $session->set('cart', $cart);
        }

        return $this->redirectToRoute('cart');
    }

    #[Route('/create-checkout-session', name: 'checkout', methods: ['POST'])]
    public function checkout(SessionInterface $session, StripeService $stripeService): JsonResponse
    {
        $cart = $session->get('cart', []);
        $lineItems = [];

        foreach ($cart as $id => $quantity) {
            $product = $this->productRepository->find($id);
            if ($product) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => ['name' => $product->getName()],
                        'unit_amount' => $product->getPrice() * 100,
                    ],
                    'quantity' => $quantity,
                ];
            }
        }

        $stripeSession = $stripeService->createCheckoutSession(
            $lineItems,
            $this->generateUrl('payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            $this->generateUrl('payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL)
        );

        return new JsonResponse(['id' => $stripeSession->id]);
    }


    #[Route('/payment-success', name: 'payment_success')]
    public function paymentSuccess(): Response
    {
        $this->addFlash('success', 'Votre paiement a été validé avec succès ! 🎉');
        return $this->redirectToRoute('app_home'); // Redirection vers la page d'accueil
    }

    #[Route('/payment-cancel', name: 'payment_cancel')]
    public function paymentCancel(): Response
    {
        $this->addFlash('error', 'Votre paiement a été annulé. ❌');
        return $this->redirectToRoute('app_home'); // Redirection vers la page d'accueil
    }




    
}
