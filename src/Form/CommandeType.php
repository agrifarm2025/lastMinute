<?php
// src/Form/CommandeType.php
namespace App\Form;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\NotBlank;

class CommandeType extends AbstractType
{
   
public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $builder
        ->add('adress', TextType::class, [
            'label' => 'Adresse de livraison',
            'constraints' => [
                new NotBlank(['message' => 'L\'adresse de livraison est obligatoire.']),
            ],
        ])
        ->add('typeCommande', ChoiceType::class, [
            'label' => 'Type de commande',
            'choices' => [
                'Standard' => 'standard',
                'Express' => 'express',
                'Sur mesure' => 'custom',
            ],
            'expanded' => false, // Utilisez false pour simplifier
            'multiple' => false,
            'required' => true,
        ])
        ->add('paiment', ChoiceType::class, [
            'label' => 'Mode de paiement',
            'choices' => [
                'Carte bancaire' => 'carte',
                'Paiement à la livraison' => 'livraison',
            ],
            'expanded' => true,
            'multiple' => false,
            'required' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}