<?php

namespace App\Form;

use App\Entity\Crop;
use App\Entity\Soildata;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SoildataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('niveau_ph', NumberType::class, [
            'label' => 'Niveau pH',
            'required' => true,
            'html5' => true,
            'attr' => [
                'step' => '0.01', // Allow decimal values
            ],
        ])
        ->add('humidite', NumberType::class, [
            'label' => 'Humidité',
            'required' => true,
            'html5' => true,
            'attr' => [
                'step' => '0.01', // Allow decimal values
            ],
        ])
        ->add('niveau_nutriment', NumberType::class, [
            'label' => 'Niveau Nutriments',
            'required' => true,
            'html5' => true,
            'attr' => [
                'step' => '0.01', // Allow decimal values
            ],
        ])
        ->add('type_sol', TextType::class, [
            'label' => 'Type de Sol',
            'required' => true,
        ]);

        
    }

public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Soildata::class, // Disable HTML5 validation for the entire form:
                'attr' => ['novalidate' => 'novalidate'],
            ]);
    }

}