<?php
namespace App\Form;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantite', NumberType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'La quantité est obligatoire']),
                    new Assert\Positive(['message' => 'La quantité doit être un nombre positif']),
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('prix', NumberType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le prix est obligatoire']),
                    new Assert\Positive(['message' => 'Le prix doit être un nombre positif']),
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('typeCommande', ChoiceType::class, [
                'choices' => [
                    'Livraison' => 'livraison',
                    'Retrait en magasin' => 'retrait',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le type de commande est obligatoire']),
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'En attente' => 'en_attente',
                    'Validée' => 'validee',
                    'Annulée' => 'annulee',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le statut est obligatoire']),
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('adress', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'L\'adresse est obligatoire']),
                ],
                'attr' => ['class' => 'form-control']
            ])
            ->add('paiment', ChoiceType::class, [
                'choices' => [
                    'Carte bancaire' => 'carte',
                    'Espèces' => 'espece',
                    'PayPal' => 'paypal',
                ],
                'expanded' => true,  // Affiche sous forme de boutons radio
                'multiple' => true,  // Permet de choisir plusieurs options
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le mode de paiement est obligatoire']),
                ],
                'attr' => ['class' => 'form-control']
            ])
            
            ->add('date_creation_commande', DateTimeType::class, [
                'widget' => 'single_text',
                
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
