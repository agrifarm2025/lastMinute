<?php
// src/Form/CommandeType.php
namespace App\Form;
// src/Form/CommandeType.php
namespace App\Form;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('adress', TextType::class, [
                'label' => 'Adresse de livraison',
                'required' => true,
            ])
            ->add('typeCommande', ChoiceType::class, [
                'label' => 'Type de commande',
                'choices' => [
                    'Standard' => 'standard',
                    'Express' => 'express',
                    'Sur mesure' => 'custom',
                ],
                'required' => true,
            ])
            ->add('paiment', ChoiceType::class, [
                'label' => 'Mode de paiement',
                'choices' => [
                    'Carte de crédit' => 'credit_card',
                    'PayPal' => 'paypal',
                    'Espèces' => 'cash',
                ],
                'multiple' => true, // Allow multiple selections
                'expanded' => true, // Render as checkboxes
                'required' => true,
            ])
            // Add other fields as needed
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
            'attr'=>['novalidate'=>'noavalidate'],
        ]);
    }
}