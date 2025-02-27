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
        $commande->setPrix($produit->getPrix());
        $commande->setQuantite(1);  // Default to 1, or you can use a dynamic value

        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commande->setStatus('Approuvé');
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
        $panier = $session->get('panier', []);
        $produitsPanier = [];
        $total = 0;

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

        $panier = $session->get('panier', []);
        $panier[$id] = ($panier[$id] ?? 0) + 1;
        $session->set('panier', $panier);

        $this->addFlash('success', 'Produit ajouté au panier avec succès !');

        return $this->redirectToRoute('agricole');
    }

    #[Route('/panier/supprimer/{id}', name: 'supprimer_du_panier')]
    public function supprimerDuPanier(int $id, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);

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
    public function commanderPanier(SessionInterface $session): Response
    {
        // Récupérer le panier depuis la session
        $panier = $session->get('panier', []);
    
        // Vérifier si le panier est vide
        if (empty($panier)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('afficher_panier');
        }
    
        // Rediriger vers la page de validation de la commande
        return $this->redirectToRoute('valider_commande');
    }

    #[Route('/valider-commande', name: 'valider_commande')]
    public function validerCommande(SessionInterface $session, EntityManagerInterface $entityManager, Request $request): Response
    {
        $panier = $session->get('panier', []);
    
        if (empty($panier)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('afficher_panier');
        }
    
        $commande = new Commande();
    
        $produitsCommande = []; // Initialisation correcte de la variable
        $total = 0;
    
        foreach ($panier as $id => $quantite) {
            $produit = $entityManager->getRepository(Produit::class)->find($id);
            if ($produit) {
                // Vérifier si la quantité demandée est disponible
                if ($produit->getQuantite() < $quantite) {
                    $this->addFlash('error', "Stock insuffisant pour le produit : " . $produit->getNom());
                    return $this->redirectToRoute('afficher_panier');
                }
    
                $produitsCommande[] = [
                    'produit' => $produit,
                    'quantite' => $quantite,
                    'prixTotal' => $produit->getPrix() * $quantite
                ];
    
                $total += $produit->getPrix() * $quantite;
            }
        }
    
        // Création du formulaire de validation de commande
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $adress = $form->get('adress')->getData();
            $typeCommande = $form->get('typeCommande')->getData();
            $paiment = $form->get('paiment')->getData();
    
            if ($typeCommande === null) {
                $this->addFlash('error', 'Le type de commande est requis.');
                return $this->redirectToRoute('valider_commande');
            }
    
            $commande->setAdress($adress);
            $commande->setTypeCommande($typeCommande);
            $commande->setPaiment($paiment);
            $commande->setDateCreationCommande(new \DateTime());
            $commande->setStatus('En attente');
    
            foreach ($produitsCommande as $item) {
                $produit = $item['produit'];
    
                // Diminuer la quantité en stock
                $produit->setQuantite($produit->getQuantite() - $item['quantite']);
    
                // Associer le produit à la commande
                $commande->addProduit($produit);
                $commande->setQuantite($commande->getQuantite() + $item['quantite']);
                $commande->setPrix($commande->getPrix() + $item['prixTotal']);
    
                $entityManager->persist($produit); // Sauvegarde des nouvelles quantités
            }
    
            $entityManager->persist($commande);
            $entityManager->flush();
    
            // Vider le panier après validation
            $session->remove('panier');
    
            $this->addFlash('success', 'Votre commande a été passée avec succès !');
    
            return $this->redirectToRoute('agricole');
        }
    
        return $this->render('commande/commande.html.twig', [
            'form' => $form->createView(),
            'produitsCommande' => $produitsCommande, // Vérification que la variable est bien transmise
            'total' => $total
        ]);
    }
}
