<?php

namespace App\Controller\Front;

use App\Controller\ApiController;
use App\DTO\User\RegisterRequest;
use App\Entity\User\User;
use App\Helper\Api\Exception\ConflictException;
use App\Helper\Api\ResponseEntity;
use App\Helper\Authenticator\Authenticator;
use App\Repository\User\UserRepository;
use App\Service\Jwt\JwtService;
use Doctrine\ORM\EntityManagerInterface;
use Random\RandomException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/users')]
class UserController extends ApiController
{
    /**
     * @var JwtService $jwtService
     */
    private JwtService $jwtService;

    /**
     * @var EntityManagerInterface $entityManager
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var UserPasswordHasherInterface $passwordHasher
     */
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * @var UserRepository $userRepository
     */
    private UserRepository $userRepository;

    /**
     * @var ResponseEntity $responseEntity
     */
    private ResponseEntity $responseEntity;

    public function __construct(
        JwtService $jwtService,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository,
        ResponseEntity $responseEntity
    ) {
        parent::__construct($responseEntity);

        $this->jwtService = $jwtService;
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
        $this->responseEntity = $responseEntity;
    }

    /**
     * @param RegisterRequest $registerRequest
     * @param Request $request
     * @return JsonResponse
     * @throws RandomException
     */
    #[Route('/register', name: 'user_register', methods: ['POST'])]
    public function register(RegisterRequest $registerRequest, Request $request): JsonResponse
    {
        if ($this->userRepository->findOneBy(['email' => $registerRequest->email])) {
            return $this->re->withException(new ConflictException('User already exists.'));
        }

        $salt = Authenticator::generateSalt();
        $password = Authenticator::combinePassword($registerRequest->password, $salt);

        $user = new User($registerRequest->email, $password, $salt);

        //TODO set role + create migrations

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->re->withMessage('User registered successfully.', Response::HTTP_CREATED);
    }
}
