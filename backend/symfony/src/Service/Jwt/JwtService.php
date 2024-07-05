<?php

namespace App\Service\Jwt;

use App\Entity\User\ApiToken;
use App\Entity\User\Account;
use App\Helper\Jwt\JwtExpiration;
use App\Helper\Jwt\JwtUsage;
use App\Repository\Account\ApiTokenRepository;
use App\Service\Account\SessionService;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class JwtService
{
    /**
     * @var JWTTokenManagerInterface $jwtManager
     */
    private JWTTokenManagerInterface $jwtManager;

    /**
     * @var EntityManagerInterface $entityManager
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var ApiTokenRepository $apiTokenRepository
     */
    private ApiTokenRepository $apiTokenRepository;

    /**
     * @var SessionService $sessionService
     */
    private SessionService $sessionService;

    /**
     * @param JWTTokenManagerInterface $jwtManager
     * @param EntityManagerInterface $entityManager
     * @param ApiTokenRepository $apiTokenRepository
     * @param SessionService $sessionService
     */
    public function __construct(
        JWTTokenManagerInterface $jwtManager,
        EntityManagerInterface $entityManager,
        ApiTokenRepository $apiTokenRepository,
        SessionService $sessionService
    )
    {
        $this->jwtManager = $jwtManager;
        $this->entityManager = $entityManager;
        $this->apiTokenRepository = $apiTokenRepository;
        $this->sessionService = $sessionService;
    }

    /**
     * Generate a token for a user based on the usage
     * @param UserInterface $user
     * @param JwtUsage $usage
     * @return string
     */
    public function generateToken(UserInterface $user, JwtUsage $usage): string
    {
        if (!$user instanceof Account) {
            throw new InvalidArgumentException('Expected an instance of ' . Account::class);
        }

        $expiration = $this->getExpirationForUsage($usage);
        $payload = [
            'user_id' => $user->getId(),
            'usage' => $usage->value,
            'exp' => (time() + $expiration),
        ];

        return $this->jwtManager->createFromPayload($user, $payload);
    }

    /**
     * Create or update a refresh token for a user.
     * @param UserInterface $user
     * @param Request $request
     * @return ApiToken
     */
    public function createOrUpdateRefreshToken(UserInterface $user, Request $request): ApiToken
    {
        if (!$user instanceof Account) {
            throw new InvalidArgumentException('Expected an instance of ' . Account::class);
        }

        $sessionId = $this->sessionService->generate($request);
        $existingToken = $this->apiTokenRepository->findByUserAndSession($user, $sessionId);

        if ($existingToken) {
            $existingToken->updateToken($this->generateToken($user, JwtUsage::USAGE_API_REFRESH));
            $this->entityManager->flush();
            return $existingToken;
        }

        $refreshToken = new ApiToken(
            $this->generateToken($user, JwtUsage::USAGE_API_REFRESH),
            $sessionId,
            $user
        );
        $this->entityManager->persist($refreshToken);
        $this->entityManager->flush();

        return $refreshToken;
    }

    /**
     * @param JwtUsage $usage
     * @return int
     */
    private function getExpirationForUsage(JwtUsage $usage): int
    {
        return match($usage) {
            JwtUsage::USAGE_API_ACCESS => JwtExpiration::EXPIRATION_10_MINUTES->value,
            JwtUsage::USAGE_API_REFRESH => JwtExpiration::EXPIRATION_1_YEAR->value,
            JwtUsage::USAGE_ACCOUNT_ACTIVATION => JwtExpiration::EXPIRATION_1_HOUR->value,
        };
    }
}
