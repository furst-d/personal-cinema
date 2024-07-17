<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends ApiController
{
    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->re->withMessage("success");
    }
}
