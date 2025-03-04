<?php

// src/Controller/ProfileController.php
namespace App\Controller;

use App\Entity\Users;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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

    #[Route('/profile/update-photo', name: 'app_update_profile_photo')]
    #[IsGranted('ROLE_USER')] // Ensures only logged-in users can access
    public function updatePhoto(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser(); // Get currently logged-in user
        $photoDir = $this->getParameter('photo_dir'); // Configured upload directory

        // Create a form for file upload
        $form = $this->createFormBuilder()
            ->add('photo', FileType::class, [
                'label' => 'Upload new profile picture',
                'mapped' => false,
                'required' => false,
            ])
            ->add('save', SubmitType::class, ['label' => 'Update Profile Picture'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('photo')->getData();

            if ($photo) {
                // Generate unique filename
                $fileName = uniqid().'.'.$photo->guessExtension();

                // Move file to the directory
                try {
                    $photo->move($photoDir, $fileName);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Error uploading the file.');
                    return $this->redirectToRoute('app_update_profile_photo');
                }

                // Delete old image if not the default
                if ($user->getImageFileName() && $user->getImageFileName() !== 'default.png') {
                    $oldImage = $photoDir . '/' . $user->getImageFileName();
                    if (file_exists($oldImage)) {
                        unlink($oldImage);
                    }
                }

                // Save new image
                $user->setImageFileName($fileName);
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Profile picture updated successfully!');
                return $this->redirectToRoute('app_update_profile_photo');
            }
        }

        return $this->render('profile/update_photo.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/profile/delete-photo', name: 'app_delete_profile_photo')]
    #[IsGranted('ROLE_USER')]
    public function deletePhoto(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $photoDir = $this->getParameter('photo_dir');

        // Delete only if not default
        if ($user->getImageFileName() && $user->getImageFileName() !== 'default.png') {
            $oldImage = $photoDir . '/' . $user->getImageFileName();
            if (file_exists($oldImage)) {
                unlink($oldImage);
            }
            $user->setImageFileName('default.png');
            $entityManager->persist($user);
            $entityManager->flush();
        }

        $this->addFlash('success', 'Profile picture removed!');
        return $this->redirectToRoute('app_profile');
    }

    
}

