<?php

namespace App\Controller;

use App\Lib\Api\Exception\ApiException;
use App\Repository\TestRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends ApiController
{
    #[Route('/', name: 'homepage')]
    public function index(TestRepository $repository): JsonResponse
    {
        $tests = $repository->findAll();
        $test = $repository->find(1);
        return $this->re->withData($tests);
    }
}
