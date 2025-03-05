<?php
namespace App\Form;

use App\Entity\Crop;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CropType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cropEvent', TextType::class, [
                'label' => 'Crop Event',
                'required' => true,
            ])
            ->add('typeCrop', ChoiceType::class,[
                'choices'=>[
                    'Blé'=>'Blé',
                    'Potato'=>'Potato',
                    'Pasteque'=>'Pasteque',
                    'Tomato'=>'Tomato'
                ],                'required' => true,

            ])
            ->add('methodeCrop', ChoiceType::class,[
                'choices'=>[
                    'Par Main'=>'Par Main',
                    'Par Machine'=>'Par Machine',

                ],
                'required' => true,

            ])
            ->add('datePlantation', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date Plantation',
                'required' => true,
            ])
            ->add('dateCrop', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date Crop',
                'required' => true,
            ])
            ->add('heureCrop', TimeType::class, [
                'widget' => 'single_text',
                'label' => 'Heure Crop',
                'required' => true,


            ])
            ->add('heurePlantation', TimeType::class, [
                'widget' => 'single_text',
                'label' => 'Heure Plantation',
                'required' => true,


            ])
            
            ->add('create', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary rounded-pill py-3 px-5'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Crop::class,
            // Disable HTML5 validation for the entire form:
                'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}