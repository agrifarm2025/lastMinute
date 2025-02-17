<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ProduitController extends AbstractController
{
    #[Route("/produit/add", name: "produit_add")]
    public function add(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $produit->setDateCreationProduit(new \DateTime());

            // Gestion de l'upload de l'image
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'), // Déplace vers public/uploads/images/
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception('Échec de l’upload du fichier.');
                }

                // Stocker uniquement le nom du fichier dans la base de données
                $produit->setImageFileName($newFilename);
            }

            $em->persist($produit);
            $em->flush();

            return $this->redirectToRoute('marketplace');
        }

        return $this->render('produit/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/marketplace", name: "marketplace")]
    public function marketplace(EntityManagerInterface $em): Response
    {
        $produits = $em->getRepository(Produit::class)->findBy(['approved' => true]);
        return $this->render('produit/marketplace.html.twig', [
            'produits' => $produits,
        ]);
    }

    #[Route('/produit/edit/{id}', name: 'produit_edit')]
public function edit(Request $request, EntityManagerInterface $em, Produit $produit, SluggerInterface $slugger): Response
{
    $form = $this->createForm(ProduitType::class, $produit);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Vérification et gestion de l'image (si modifiée)
        $imageFile = $form->get('imageFile')->getData();
        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

            try {
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
                throw new \Exception('Échec de l’upload du fichier.');
            }

            $produit->setImageFileName($newFilename);
        }

        $em->flush();
        return $this->redirectToRoute('marketplace');
    }

    return $this->render('produit/edit.html.twig', [
        'form' => $form->createView(),
        'produit' => $produit,
    ]);
}


    #[Route('/produit/delete/{id}', name: 'produit_delete')]
    public function delete(EntityManagerInterface $em, Produit $produit): Response
    {
        $em->remove($produit);
        $em->flush();
        return $this->redirectToRoute('marketplace');
    }

    
#[Route('/produit/modifier/{id}', name: 'produit_modifier')]

public function updatefield(ManagerRegistry $m,Request $req,ProduitRepository $rep ,$id) // Use ManagerRegistry here  
    {  
        $em = $m->getManager();  
        $farm=$rep->find($id); 
        $form=$this->createForm(ProduitType::class,$farm);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()){
        $em->persist($farm);  
        $em->flush();  
        return $this->redirectToRoute('marketplace');
        }
        return $this->render("edit.html.twig",[
            'form'=>$form
        ]);  
    } 
    #[Route("/admin", name: "admin")]
public function admin(EntityManagerInterface $em): Response
{
    // Récupérer tous les produits (approuvés et non approuvés)
    $produits = $em->getRepository(Produit::class)->findAll();

    return $this->render('produit/aprouver.html.twig', [
        'produits' => $produits,
    ]);
}
    #[Route('/produit/approve/{id}', name: 'produit_approve')]
    public function approveProduct(EntityManagerInterface $em, Produit $produit): Response
    {
        $produit->setApproved(true); // Approve the product
        $em->flush(); // Save changes to the database
    
        // Flash message to confirm the action
        $this->addFlash('success', 'Produit approuvé avec succès !');
    
        // Redirect to the "agricole" page to show approved products
        return $this->redirectToRoute('agricole');
    }
    #[Route('/agricole', name: 'agricole')]
public function agricole(EntityManagerInterface $em, SessionInterface $session): Response
{
    // Récupérer les produits approuvés
    $produits = $em->getRepository(Produit::class)->findBy(['approved' => true]);

    // Récupérer le panier depuis la session
    $panier = $session->get('panier', []);

    return $this->render('produit/agricole.html.twig', [
        'produits' => $produits,
        'panier' => $panier, // 🛒 Passer le panier à la vue
    ]);
}

#[Route('/recherche', name: 'recherche_produit', methods: ['GET'])]
    public function recherche(Request $request, ProduitRepository $produitRepository): Response
    {
        // Récupérer le terme de recherche depuis la requête
        $term = $request->query->get('q');

        // Rechercher les produits correspondants
        $produits = $produitRepository->findByNom($term);

        // Afficher la vue avec les résultats
        return $this->render('produit/agricole.html.twig', [
            'produits' => $produits,
        ]);
    }
}
