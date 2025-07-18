<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PreflightController
{
    #[Route('/api/{any}', name: 'preflight', methods: ['OPTIONS'], requirements: ['any' => '.+'])]
    public function preflight(): Response
    {
        $response = new Response();
        $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:5173');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type ,Authorization');
        return $response;
    }
}
