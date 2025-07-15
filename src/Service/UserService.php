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
     * Registers a new user with the system
     * 
     * @param array $data User registration data containing:
     *               - email (required)
     *               - password (required)
     *               - username (optional)
     * @return array Registered user details including:
     *               - id
     *               - email
     *               - roles
     * @throws AlreadyLoggedInException If a user is already authenticated
     * @throws MissingCredentialsException If required fields are missing
     * @throws InvalidRegistrationDataException If validation fails
     */
    public function registerUser(array $data): array
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

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles()
        ];
    }

    /**
     * Authenticates a user and generates a JWT token
     * 
     * @param array $data Authentication credentials containing:
     *               - email (required)
     *               - password (required)
     * @return array Authentication response containing:
     *               - token (JWT token)
     *               - user (user details)
     * @throws MissingCredentialsException If required fields are missing
     * @throws InvalidCredentialsException If authentication fails
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
     * Validates that all required fields are present in the input data
     * 
     * @param array $data Input data to validate
     * @param array $requiredFields List of required field names
     * @throws MissingCredentialsException If any required field is missing
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