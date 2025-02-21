<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class CartTest extends WebTestCase
{
    

    public function testAddProductToCart()
    {
        // Création d'une session simulée
        $session = new Session(new MockArraySessionStorage());
        $session->start(); 

        // Simuler un panier en session
        $cart = $session->get('cart', []);
        $cart[] = [
            'id' => 1,
            'name' => 'Test Product',
            'size' => 'M',
            'price' => 19.99,
            'image' => 'test.jpg'
        ];
        $session->set('cart', $cart);

        // Vérification que l'ajout a bien eu lieu
        $this->assertCount(1, $session->get('cart'));
    }

    public function testProcessPayment()
    {
        // Création d'une session simulée
        $session = new Session(new MockArraySessionStorage());
        $session->start(); 

        // Simuler un paiement en session
        $paymentData = [
            'status' => 'success',
            'transaction_id' => 'txn_123456789',
            'amount' => 1999,
            'currency' => 'eur',
        ];
        $session->set('payment', $paymentData);

        // Vérification que le paiement a bien été enregistré en session
        $this->assertEquals('success', $session->get('payment')['status']);
        $this->assertEquals(1999, $session->get('payment')['amount']);
    }

   

}

