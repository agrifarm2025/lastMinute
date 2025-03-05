<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Users;
use App\Repository\UsersRepository;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class OAuthRegistrationService
{
    /**
     * @param GoogleUser $resourceOwner
     */
    public function persist(ResourceOwnerInterface $resourceOwner, UsersRepository $repository): Users
    {
        $user = (new Users())
            ->setEmail($resourceOwner->getEmail())
            ->setGoogleId($resourceOwner->getId());

        $repository->add($user, true);
        return $user;
    }
}