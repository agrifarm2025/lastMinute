<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Produit;
use App\Form\CommandeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
    #[Route('/commande/{id}', name: 'commande_produit')]
    public function commander(Produit $produit, Request $request, EntityManagerInterface $entityManager): Response
    {
        $commande = new Commande();
        $commande->setPrix($produit->getPrix()); // Assurez-vous que la classe Commande a bien une méthode setPrix()
        $commande->setQuantite($produit->getQuantite()); // Assurez-vous que la classe Commande a bien une méthode setQuantite()

        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commande->setStatus('Approuvé'); // Modifier le statut lors de l'approbation
            $entityManager->persist($commande);
            $entityManager->flush();

            $this->addFlash('success', 'Commande approuvée et enregistrée avec succès !');

            return $this->redirectToRoute('agricole');
        }

        return $this->render('commande/commande.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit
        ]);
    }

    #[Route('/agricole', name: 'agricole')]
    public function agricole(): Response
    {
        return $this->render('agricole.html.twig');
    }

    #[Route('/panier', name: 'afficher_panier')]
    public function afficherPanier(SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        // Récupérer le panier depuis la session
        $panier = $session->get('panier', []);

        // Initialiser un tableau pour stocker les produits du panier avec leurs détails
        $produitsPanier = [];
        $total = 0;

        // Parcourir les éléments du panier pour récupérer les détails des produits
        foreach ($panier as $id => $quantite) {
            $produit = $entityManager->getRepository(Produit::class)->find($id);
            if ($produit) {
                $produitsPanier[] = [
                    'produit' => $produit,
                    'quantite' => $quantite,
                    'prixTotal' => $produit->getPrix() * $quantite
                ];
                $total += $produit->getPrix() * $quantite;
            }
        }

        return $this->render('commande/panier.html.twig', [
            'produitsPanier' => $produitsPanier,
            'total' => $total
        ]);
    }

    #[Route('/panier/ajouter/{id}', name: 'ajouter_au_panier')]
    public function ajouterAuPanier(int $id, SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $produit = $entityManager->getRepository(Produit::class)->find($id);
        if (!$produit) {
            throw $this->createNotFoundException('Produit introuvable');
        }

        // Récupérer le panier depuis la session
        $panier = $session->get('panier', []);

        // Ajouter ou mettre à jour la quantité du produit dans le panier
        $panier[$id] = ($panier[$id] ?? 0) + 1;
        $session->set('panier', $panier);

        $this->addFlash('success', 'Produit ajouté au panier avec succès !');

        return $this->redirectToRoute('agricole');
    }

    #[Route('/panier/supprimer/{id}', name: 'supprimer_du_panier')]
    public function supprimerDuPanier(int $id, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);

        // Supprimer le produit du panier
        if (isset($panier[$id])) {
            unset($panier[$id]);
            $session->set('panier', $panier);
            $this->addFlash('success', 'Produit supprimé du panier avec succès !');
        } else {
            $this->addFlash('error', 'Produit introuvable dans le panier.');
        }

        return $this->redirectToRoute('afficher_panier');
    }

    #[Route('/commander-panier', name: 'commander_panier')]
    public function commanderPanier(SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        // Récupérer le panier depuis la session
        $panier = $session->get('panier', []);

        // Vérifier si le panier est vide
        if (empty($panier)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('afficher_panier');
        }

        // Créer une commande pour chaque produit dans le panier
        foreach ($panier as $id => $quantite) {
            $produit = $entityManager->getRepository(Produit::class)->find($id);
            if ($produit) {
                $commande = new Commande();
                
                $commande->setQuantite($quantite);
                $commande->setPrix($produit->getPrix() * $quantite);
                $commande->setStatus('En attente'); // Statut initial de la commande

                $entityManager->persist($commande);
            }
        }

        // Enregistrer les commandes en base de données
        $entityManager->flush();

        // Vider le panier après la commande
        $session->remove('panier');

        // Message de succès
        $this->addFlash('success', 'Votre commande a été passée avec succès !');

        // Rediriger vers la page du panier ou une autre page
        return $this->redirectToRoute('afficher_panier');
    }
}