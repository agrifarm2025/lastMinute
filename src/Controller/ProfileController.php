<?php

// src/Controller/ProfileController.php
namespace App\Controller;

use App\Entity\Users;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profile(Request $request, EntityManagerInterface $em, TokenStorageInterface $tokenStorage): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Vérifier que l'utilisateur est bien connecté et est une instance de Users
        if (!$user || !$user instanceof Users) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        // Créer et gérer le formulaire de mise à jour du profil
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        // Gérer la mise à jour du profil
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Profil mis à jour avec succès !');
            return $this->redirectToRoute('app_profile');
        }

        // Gérer la suppression du compte
        if ($request->isMethod('POST')) {
            $csrfToken = $request->request->get('_token');
            if ($this->isCsrfTokenValid('delete-account', $csrfToken)) {
                $em->remove($user);
                $em->flush();

                // Déconnecter l'utilisateur après la suppression
                $tokenStorage->setToken(null);
                $this->addFlash('success', 'Votre compte a été supprimé.');

                return $this->redirectToRoute('app_login');
            } else {
                $this->addFlash('danger', 'Token CSRF invalide.');
            }
        }

        return $this->render('profile/profile.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}