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
        $user = $this->getUser();
    
        // Create and handle profile update form
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);
    
        // Handle profile update
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Profile updated successfully!');
            return $this->redirectToRoute('app_profile');
        }
    
        // Handle account deletion
        if ($request->isMethod('POST') && $request->request->has('delete_account')) {
            $em->remove($user);
            $em->flush();
    
            // Log out the user after deletion
            $tokenStorage->setToken(null);
            $this->addFlash('success', 'Your account has been deleted.');
    
            return $this->redirectToRoute('app_login');
        }
    
        return $this->render('profile/profile.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    
}

