<?php

// src/Form/ProductSizeType.php
namespace App\Form;

use App\Entity\ProductSize;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductSizeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                        
            // Quantité de cette taille
            ->add('quantity', NumberType::class, [
                'label' => 'Quantité',
                'attr' => ['min' => 0],  // La quantité ne peut pas être négative
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductSize::class,
        ]);
    }
}







