<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Repository\UsersRepository;
use App\Security\LoginFormAuthenticator;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager,SendMailService $mail, JWTService $jwt): Response
    {
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('password')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            //  génère le JWT de l'utilisateur
            //  crée le Header
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            //  crée le Payload
            $payload = [
                'user_id' => $user->getId()
            ];

            //  génère le token
            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

           // dd($token) ;

            //  envoie un mail
            $mail->send(
                'no-reply@Agrifarm.net',
                $user->getEmail(),
                'Activation de votre compte',
                'register',
                compact('user','token')
            );

            return $security->login($user, LoginFormAuthenticator::class, 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verif/{token}', name: 'verify_user')]
    public function verifyUser($token, JWTService $jwt, UsersRepository $usersRepository, EntityManagerInterface $em): Response
    {

        // dd($jwt->isValid($token)) ;
        // dd($jwt->getPayload($token)) ;
        // dd($jwt->getHeader($token)) ;
        // dd($jwt->isExpired($token)) ;
        //dd($jwt->check($token,$this->getParameter('app.jwtsecret'))) ;

         // vérifie si le token est valide, n'a pas expiré et n'a pas été modifié
         if($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))){

            //  récupère le payload
            $payload = $jwt->getPayload($token);

            //  récupère le user du token
            $user = $usersRepository->find($payload['user_id']);

            // vérifie que l'utilisateur existe et n'a pas encore activé son compte
            if($user && !$user->isVerified()){
                $user->setIsVerified(true);
                $em->flush($user);
                $this->addFlash('success', 'Utilisateur activé');
                return $this->redirectToRoute('app_profile');
            }
        }
        // Ici un problème se pose dans le token
        $this->addFlash('danger', 'Le token est invalide ou a expiré');

        return $this->redirectToRoute('app_login');
    }
    #[Route('/renvoiverif', name: 'resend_verif')]
    public function resendVerif(JWTService $jwt, SendMailService $mail, UsersRepository $usersRepository): Response
    {
        $user = $this->getUser();

        if(!$user){
            $this->addFlash('danger', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_login');    
        }

        if($user->isVerified()){
            $this->addFlash('danger', 'Cet utilisateur est déjà activé');
            return $this->redirectToRoute('app_profile');    
        }

        //  génère le JWT de l'utilisateur
        //  crée le Header
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        //  crée le Payload
        $payload = [
            'user_id' => $user->getId()
        ];

        //  génère le token
        $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

        //  envoie un mail
        $mail->send(
            'no-reply@Agrifarm.net',
            $user->getEmail(),
            'Activation de votre compte',
            'register',
            compact('user', 'token')
        );
        $this->addFlash('success', 'Email de vérification envoyé');
        return $this->redirectToRoute('app_profile');
    }

}
