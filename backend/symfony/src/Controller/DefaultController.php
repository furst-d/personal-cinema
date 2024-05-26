<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        try {
            $connection = $em->getConnection();
            $connection->connect();

            if ($connection->isConnected()) {
                $message = 'Database connection is successful!';
            } else {
                $message = 'Failed to connect to the database.';
            }
        } catch (Exception $e) {
            $message = 'Error: ' . $e->getMessage();
        }

        return $this->json([
            'message' => $message,
            'path' => 'src/Controller/DefaultController.php',
        ]);
    }
}
