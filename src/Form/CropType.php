<?php

namespace App\Form;

use App\Entity\Crop;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CropType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $cropChoices = $options['crop_choices'] ?? [];

        $builder
            ->add('cropEvent', TextType::class, [
                'label' => 'Crop Event',
                'required' => true,
                'attr' => ['placeholder' => 'Enter crop event'],
                'constraints' => [
                    new NotBlank(['message' => 'Crop Event cannot be empty.'])
                ],
            ])
            ->add('typeCrop', ChoiceType::class, [
                'label' => 'Crop Type',
                'choices' => array_combine($cropChoices, $cropChoices),
                'placeholder' => 'Select a Crop Type',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'You must select a crop type.'])
                ],
            ])
            ->add('methodeCrop', ChoiceType::class, [
                'label' => 'Method of Cultivation',
                'choices' => [
                    'Par Main' => 'Par Main',
                    'Par Machine' => 'Par Machine',
                ],
                'placeholder' => 'Select a Method',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'You must select a cultivation method.'])
                ],
            ])
            ->add('datePlantation', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date Plantation',
                'required' => true,
            ])
            ->add('heurePlantation', TimeType::class, [
                'widget' => 'single_text',
                'label' => 'Time of Plantation',
                'required' => true,
            ])
            ->add('dateCrop', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date Crop',
                'required' => true,
            ])
            ->add('heureCrop', TimeType::class, [
                'widget' => 'single_text',
                'label' => 'Time of Crop',
                'required' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Submit Crop Data',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Crop::class,
            'attr' => ['novalidate' => 'novalidate'], // Disable HTML5 validation
            'crop_choices' => [] // Default empty array if no crops are provided
        ]);
    }
}