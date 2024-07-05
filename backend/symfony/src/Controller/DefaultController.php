<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends ApiController
{
    #[Route('/', name: 'homepage')]
    public function index(): JsonResponse
    {
        $payload = [
            'message' => 'Welcome to your new controller! Test 1',
            'path' => 'src/Controller/DefaultController.php',
        ];
        return $this->re->withData($payload);
    }
}
