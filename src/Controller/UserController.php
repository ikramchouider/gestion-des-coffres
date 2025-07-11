<?php

namespace App\Controller;

use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user', format: 'json')]
class UserController extends AbstractController
{
    public function __construct(private UserService $userService) {}

    #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $this->userService->registerUser($data);

        return new JsonResponse(
            ['success' => 'Registration successful'],
            Response::HTTP_CREATED
        );
    }

    #[Route('/login', name: 'app_user_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $responseData = $this->userService->authenticateUser($data);

        return new JsonResponse($responseData, Response::HTTP_OK);
    }
}