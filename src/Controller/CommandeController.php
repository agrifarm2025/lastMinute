<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Produit;
use App\Form\CommandeType;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Users;

use Dompdf\Dompdf;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


class CommandeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/commande/{id}', name: 'commande_produit')]
    public function commander(Produit $produit, Request $request): Response
    {
        $commande = new Commande();
        $commande->setPrix($produit->getPrix());
        $commande->setQuantite(1);  // Default to 1, or you can use a dynamic value

        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commande->setStatus('Approuvé');
            $this->entityManager->persist($commande);
            $this->entityManager->flush();

            $this->addFlash('success', 'Commande approuvée et enregistrée avec succès !');
            return $this->redirectToRoute('agricole');
        }

        return $this->render('commande/commande.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit,
        ]);
    }

    #[Route('/agricole', name: 'agricole')]
    public function agricole(): Response
    {
        return $this->render('agricole.html.twig');
    }

    #[Route('/panier', name: 'afficher_panier')]
    public function afficherPanier(SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        $produitsPanier = [];
        $total = 0;

        foreach ($panier as $id => $quantite) {
            $produit = $this->entityManager->getRepository(Produit::class)->find($id);
            if ($produit) {
                $produitsPanier[] = [
                    'produit' => $produit,
                    'quantite' => $quantite,
                    'prixTotal' => $produit->getPrix() * $quantite,
                ];
                $total += $produit->getPrix() * $quantite;
            }
        }

        return $this->render('commande/panier.html.twig', [
            'produitsPanier' => $produitsPanier,
            'total' => $total,
        ]);
    }

    #[Route('/panier/ajouter/{id}', name: 'ajouter_au_panier')]
    public function ajouterAuPanier(int $id, SessionInterface $session): Response
    {
        $produit = $this->entityManager->getRepository(Produit::class)->find($id);

        if (!$produit) {
            $this->addFlash('error', 'Le produit demandé n\'existe pas.');
            return $this->redirectToRoute('agricole');
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
        $panier = $session->get('panier', []);

        if (empty($panier)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('afficher_panier');
        }
        return $this->redirectToRoute('valider_commande');
    }

    #[Route('/valider-commande', name: 'valider_commande')]
    public function validerCommande(SessionInterface $session, Request $request)
    {
        $panier = $session->get('panier', []);
        if (empty($panier)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('afficher_panier');
        }

        $user = $this->getUser();
        if (!$user instanceof Users) {
            $this->addFlash('error', 'Vous devez être connecté pour passer une commande.');
            return $this->redirectToRoute('app_register');
        }

        $commande = new Commande();
        $produitsCommande = [];
        $total = 0;

        foreach ($panier as $id => $quantite) {
            $produit = $this->entityManager->getRepository(Produit::class)->find($id);
            if ($produit) {
                $prixTotal = $produit->getPrix() * $quantite;
                $produitsCommande[] = [
                    'produit' => $produit,
                    'quantite' => $quantite,
                    'prixTotal' => $prixTotal,
                ];
                $total += $prixTotal;
            }
        }

        $reductionAppliquee = $this->verifierReduction($user, $total);
        if ($reductionAppliquee) {
            $total *= 0.8;
        }

        $validatedCommandsCount = $this->entityManager->getRepository(Commande::class)
            ->count(['user' => $user, 'status' => 'Approuvé']);

        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commande->setQuantite(array_sum(array_column($produitsCommande, 'quantite')));
            $commande->setPrix($total);
            $commande->setStatus('Approuvé');
            $commande->setDateCreationCommande(new \DateTimeImmutable('today'));
            $commande->setUser($user);
            $commande->setTypeCommande('Standard');

            foreach ($produitsCommande as $item) {
                $produit = $item['produit'];
                $quantite = $item['quantite'];

                if ($quantite === null) {
                    throw new \Exception("La quantité ne peut pas être nulle pour le produit : " . $produit->getId());
                }

                $produit->setQuantite($produit->getQuantite() - $quantite);
                $commande->addProduit($produit);
                $this->entityManager->persist($produit);
            }

            $this->entityManager->persist($commande);
            $this->entityManager->flush();

            $session->remove('panier');


    

            return $this->redirectToRoute('checkout',['id'=>$commande->getId()]);
        }

        return $this->render('commande/commande.html.twig', [
            'form' => $form->createView(),
            'produitsCommande' => $produitsCommande,
            'total' => $total,
            'reductionAppliquee' => $reductionAppliquee,
            'validatedCommandsCount' => $validatedCommandsCount,
        ]);
    }


    private function verifierReduction(Users $user, float $total): bool
    {
        $nombreCommandes = $this->entityManager->getRepository(Commande::class)
            ->count(['user' => $user, 'status' => 'Approuvé']);

        return $nombreCommandes > 5;
    }
}
