<?php

namespace App\Service;

use App\Entity\User;
use App\Exception\AlreadyLoggedInException;
use App\Exception\InvalidCredentialsException;
use App\Exception\InvalidRegistrationDataException;
use App\Exception\MissingCredentialsException;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private ValidatorInterface $validator,
        private Security $security,
        private JWTTokenManagerInterface $jwtManager
    ) {}

    /**
     * Handles user registration
     */
    public function registerUser(array $data): User
    {
        if ($this->security->getUser()) {
            throw new AlreadyLoggedInException();
        }

        $this->validateRequiredFields($data, ['email', 'password']);

        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword($this->passwordHasher->hashPassword($user, $data['password']));

        if (isset($data['username'])) {
            $user->setUsername($data['username']);
        }

        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            throw new InvalidRegistrationDataException($errors);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * Handles user authentication
     */
    public function authenticateUser(array $data): array
    {
        $this->validateRequiredFields($data, ['email', 'password']);

        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => $data['email']]);

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $data['password'])) {
            throw new InvalidCredentialsException();
        }

        return [
            'token' => $this->jwtManager->create($user),
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ]
        ];
    }

    /**
     * Validates required fields in input data
     */
    private function validateRequiredFields(array $data, array $requiredFields): void
    {
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                throw new MissingCredentialsException(sprintf('Missing required field: %s', $field));
            }
        }
    }
}