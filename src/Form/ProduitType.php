<?php

namespace App\Form;

use App\Entity\Produit;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('description')
            ->add('quantite')
            ->add('prix', NumberType::class, [
                'label' => 'Prix',
                'required' => true,
                'attr' => ['class' => 'form-control', 'placeholder' => 'Prix du produit']
            ])
            ->add('categories')
           
             ->add('date_creation_produit', DateTimeType::class, [
        'widget' => 'single_text',
        'required' => false,
    ])
    ->add('date_modification_produit', DateTimeType::class, [
        'widget' => 'single_text',
        'required' => false,  
    ])

            ->add('imageFile', FileType::class, [
                'label' => 'Product Image',
                'mapped' => false, 
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                        'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG, WebP)',
                    ])
                ],
            ])
            
;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
            'attr'=>['novalidate'=>'noavalidate'],
        ]);
    }
}
