<?php

// src/Form/ProductType.php
namespace App\Form;

use App\Entity\Product;
use App\Entity\ProductSizeStock;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('price')
            ->add('imageFilename')
            // Collection des tailles et quantités
            ->add('sizeStocks', CollectionType::class, [
                'entry_type' => ProductSizeStockType::class, // Type de formulaire pour ProductSizeStock
                'entry_options' => ['label' => false],  // Pas de label pour chaque entrée
                'allow_add' => true,  // Permet l'ajout dynamique via JavaScript
                'by_reference' => false,  // Permet de gérer les entités correctement
                'prototype' => true, // Permet d'ajouter de nouveaux éléments dynamiquement
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,  // L'entité associée est Product
        ]);
    }
}



