<?php

namespace App\Form;
namespace App\Enum;

use App\Entity\Cropgrowth;
use App\Entity\Stages;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

enum StageCroissanceType: string
{
    case GERMINATION = 'germination';
    case CROISSANCE = 'croissance';
    case MATURITE = 'maturité';
    case RECOLTE = 'récolte';
}

class StageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
        ->add('name', ChoiceType::class, [
            'choices' => [
                'Plantation' => 'Plantation',
                'Croissance' => 'Croissance',
                'Maturité' => 'Maturité',
                'Récolte' => 'Récolte',
            ],
        ])
            ->add('startdate', null, [
                'widget' => 'single_text',
            ])
            ->add('enddate', null, [
                'widget' => 'single_text',
            ])
            ->add('cropgrowth', EntityType::class, [
                'class' => Cropgrowth::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stages::class,
        ]);
    }
}
