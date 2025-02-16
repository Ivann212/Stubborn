<?php

// src/Form/ProductType.php
namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;




class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('price')
            ->add('imageFilename', FileType::class, [
                'label' => 'Image du produit',
                'mapped' => false,
                'required' => false,
            ])
            ->add('sizeStocks', CollectionType::class, [
                'entry_type' => ProductSizeType::class, // Formulaire pour chaque taille et quantité
                'entry_options' => ['label' => false],
                'allow_add' => true,  // Permet l'ajout dynamique des tailles
                'by_reference' => false,  // Nécessaire pour manipuler correctement les objets entité
                'prototype' => false, // Nous n'utilisons pas de prototype ici
            ])
            ->add('isFeatured', CheckboxType::class, [
                'label'    => 'Mettre en avant',
                'required' => false,
            ]);
            
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}






