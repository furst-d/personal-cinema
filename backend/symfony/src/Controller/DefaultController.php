<?php

namespace App\Controller;

use App\Repository\TestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(TestRepository $repository, SerializerInterface $serializer): JsonResponse
    {
       $tests = $repository->findAll();
       return new JsonResponse($serializer->serialize($tests, 'json'), 200, [], true);
    }
}
