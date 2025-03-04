<?php

namespace App\Form;

use App\Entity\Soildata;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
                'attr' => ['step' => '0.01'], // Allow decimal values
            ])
            ->add('humidite', NumberType::class, [
                'label' => 'Humidité',
                'required' => true,
                'html5' => true,
                'attr' => ['step' => '0.01'], // Allow decimal values
            ])
            ->add('niveau_nutriment', NumberType::class, [
                'label' => 'Niveau Nutriments',
                'required' => true,
                'html5' => true,
                'attr' => ['step' => '0.01'], // Allow decimal values
            ])
            ->add('type_sol', ChoiceType::class, [
                'label' => 'Type de Sol',
                'required' => true,
                'choices' => $options['soil_types'], // Use soil_types option passed from the controller
                'placeholder' => 'Sélectionner un type de sol',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Soildata::class,
            'soil_types' => [], // ✅ Define soil_types as an option to avoid errors
        ]);
    }
}
