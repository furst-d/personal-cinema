<?php

namespace App\Controller\V1\Front;

use App\Controller\ApiController;
use App\DTO\User\RegisterRequest;
use App\Helper\Api\Exception\ConflictException;
use App\Helper\Api\Exception\InternalException;
use App\Helper\Api\Exception\NotFoundException;
use App\Helper\Api\ResponseEntity;
use App\Service\User\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/users')]
class UserController extends ApiController
{
    /**
     * @var UserService $userService
     */
    private UserService $userService;

    public function __construct(ResponseEntity $re, EntityManagerInterface $em, UserService $userService) {
        parent::__construct($re, $em);
        $this->userService = $userService;
    }

    /**
     * @param RegisterRequest $registerRequest
     * @return JsonResponse
     */
    #[Route('/register', name: 'user_register', methods: ['POST'])]
    public function register(RegisterRequest $registerRequest): JsonResponse
    {
        try {
            $this->userService->registerUser($registerRequest->email, $registerRequest->password);
            return $this->re->withMessage('User registered successfully.', Response::HTTP_CREATED);
        } catch (ConflictException|InternalException|NotFoundException $e) {
            return $this->re->withException($e);
        }
    }
}
