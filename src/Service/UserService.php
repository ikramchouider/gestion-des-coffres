<?php

namespace App\Service;

use App\Entity\User;

class UserService
{
    /**
     * Create new user
     */
    public function addUser($user)
    {

        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);

        if (!$user || !$passwordHasher->isPasswordValid($user, $data['password'])) {
            return new JsonResponse(['error' => 'Invalid credentials'], 401);
        }

    }

    /**
     * Get user by its email
     */
    public function getUser(string $email): User
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);

        if (!$user || !$passwordHasher->isPasswordValid($user, $data['password'])) {
            return new JsonResponse(['error' => 'Invalid credentials'], 401);
        }

        return $user;
    }
}
