<?php

namespace App\Form;

use App\Entity\Farm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
class FarmType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('location', TextType::class, ['label' => 'Location'])
        ->add('name', TextType::class, ['label' => 'Farm Name'])
        ->add('surface', NumberType::class, ['label' => 'Surface Area'])
        ->add('adress', TextType::class, ['label' => 'Address'])
        ->add('budget', NumberType::class, ['label' => 'Budget'])
        ->add('weather', TextType::class, ['label' => 'Weather Info'])
        ->add('description', TextType::class, ['label' => 'Description'])
        ->add('bir', CheckboxType::class, ['label' => 'BIR Compliance']) // Example for boolean
        ->add('photovoltaic', CheckboxType::class, ['label' => 'Photovoltaic System'])
        ->add('fence', CheckboxType::class, ['label' => 'Fence'])
        ->add('irrigation', CheckboxType::class, ['label' => 'Irrigation System'])
        ->add('cabin', CheckboxType::class, ['label' => 'Cabin'])
        ->add('create', SubmitType::class, [
                
                'attr' => ['class' => 'btn btn-primary rounded-pill py-3 px-5']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Farm::class,
        ]);
    }
}
