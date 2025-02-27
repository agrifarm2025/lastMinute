<?php

namespace App\Security;


use App\Entity\Users;
use App\Repository\UsersRepository;
use App\Service\OAuthRegistrationService;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

abstract class AbstractOAuthAuthenticator extends OAuth2Authenticator
{
    use TargetPathTrait;
    protected string $serviceName = '';

    public function __construct(
        private readonly ClientRegistry $clientRegistry,
        private readonly RouterInterface $router,
        private readonly UsersRepository $repository,
        private readonly OAuthRegistrationService $registrationService
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return 'auth_oauth_check' === $request->attributes->get('_route') &&
            $request->get('service') === $this->serviceName;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $targetPath = $this->getTargetPath($request->getSession(), $firewallName);
        if ($targetPath) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->router->generate('app_client'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if ($request->hasSession()) {
            $request->getSession()->set(SecurityRequestAttributes::AUTHENTICATION_ERROR, $exception);
        }

        return new RedirectResponse($this->router->generate('auth_oauth_login'));
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {
        $credentials = $this->fetchAccessToken($this->getClient());
        $resourceOwner = $this->getResourceOwnerFromCredentials($credentials);
        
        $user = $this->getUserFromResourceOwner($resourceOwner, $this->repository);
    
        if (!$user) {
            // Create a new user if not found
            $user = new Users();
            $user->setEmail($resourceOwner->getEmail());
            $user->setFirstName($resourceOwner->getFirstName() ?? 'Google User');
            $user->setLastName($resourceOwner->getLastName() ?? '');
            $user->setGoogleId($resourceOwner->getId());
            $user->setRoles(['ROLE_USER']);
            
            // Generate and set a secure random password
            $password = $this->generateSecurePassword(12);
            $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
    
            // Persist the new user in the database
            $this->repository->save($user, true);
        }
    
        return new SelfValidatingPassport(
            new UserBadge($user->getUserIdentifier(), fn () => $user),
            [new RememberMeBadge()]
        );
    }

    private function generateSecurePassword(int $length = 12): string
{
    $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $lowercase = 'abcdefghijklmnopqrstuvwxyz';
    $digits = '0123456789';
    $symbols = '!@#$%^&*()-_=+<>?';

    // Ensure at least one character from each required set
    $password = $uppercase[random_int(0, strlen($uppercase) - 1)]
        . $lowercase[random_int(0, strlen($lowercase) - 1)]
        . $digits[random_int(0, strlen($digits) - 1)]
        . $symbols[random_int(0, strlen($symbols) - 1)];

    // Fill the remaining characters randomly
    $allChars = $uppercase . $lowercase . $digits . $symbols;
    for ($i = 4; $i < $length; $i++) {
        $password .= $allChars[random_int(0, strlen($allChars) - 1)];
    }

    // Shuffle the password to avoid predictable patterns
    return str_shuffle($password);
}

    


    protected function getResourceOwnerFromCredentials(AccessToken $credentials): ResourceOwnerInterface
    {
        return $this->getClient()->fetchUserFromToken($credentials);
    }

    private function getClient(): OAuth2ClientInterface
    {
        return $this->clientRegistry->getClient($this->serviceName);
    }

    abstract protected function getUserFromResourceOwner(ResourceOwnerInterface $resourceOwner, UsersRepository $repository): ?Users;
}