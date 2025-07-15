<?php

namespace App\Controller;

use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user', name: 'app_user_', format: 'json')]
class UserController extends AbstractController
{
    public function __construct(private UserService $userService) {}

    /**
     * Registers a new user with the provided data
     */
    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $response = $this->userService->registerUser($data);

        return new JsonResponse($response, Response::HTTP_CREATED);
    }

    /**
     * Authenticates a user and returns login credentials
     */
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $responseData = $this->userService->authenticateUser($data);

        return new JsonResponse($responseData, Response::HTTP_OK);
    }
}