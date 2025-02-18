<?php

namespace App\Form;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\StringToFloatTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateType ;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
       
    $builder
    ->add('name', TextType::class, ['label' => 'Task Name'])
    ->add('description', TextType::class, ['label' => 'Description'])
    
    
    ->add('date', DateType::class, [
        'widget' => 'single_text',
        'data' =>  new \DateTime('2003-03-20'), 

       ])
            ->add('ressource', TextType::class, ['label' => 'Resources'])
    ->add('responsable', TextType::class, ['label' => 'Responsible'])
    ->add('priority', ChoiceType::class, [
        'label' => 'Priority',
        'choices' => [
            'High' => 'High',
            'Medium' => 'Medium',
            'Low' => 'Low',
        ],
        
    ])
    ->add('estimated_duration', TextType::class, ['label' => 'Estimated Duration'])
    ->add('deadline',DateType::class, [
        'widget' => 'single_text',
        'data' =>  new \DateTime('2003-03-20'), 
        ])
    ->add('workers', NumberType::class, ['label' => 'Workers'])
    ->add('payment_worker')

        ->add('create', SubmitType::class, [
            'attr' => ['class' => 'btn btn-primary rounded-pill py-3 px-5'],
        ]);
}


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
