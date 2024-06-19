<?php

namespace App\Controller;

use App\Repository\TestRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends ApiController
{
    #[Route('/', name: 'homepage')]
    public function index(TestRepository $repository): JsonResponse
    {
//        $tests = $repository->findAll();
//        $test = $repository->find(1);
        $payload = [
            'message' => 'Welcome to your new controller! Test 6',
            'path' => 'src/Controller/DefaultController.php',
        ];
        return $this->re->withData($payload);
    }
}
