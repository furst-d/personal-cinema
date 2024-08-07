<?php

namespace App\Listener;

use App\Entity\Account\Account;
use App\Exception\ForbiddenException;
use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use App\Helper\Api\ResponseEntity;
use App\Helper\Jwt\JwtUsage;
use App\Service\Account\RoleService;
use App\Service\Account\TokenUserProvider;
use App\Service\Jwt\JwtService;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class TokenValidatorListener
{
    /**
     * @var JwtService $jwtService
     */
    private JwtService $jwtService;

    /**
     * @var RoleService $roleService
     */
    private RoleService $roleService;

    /**
     * @var ResponseEntity $re
     */
    private ResponseEntity $re;

    /**
     * @var TokenUserProvider $userProvider
     */
    private TokenUserProvider $userProvider;

    /**
     * @param JwtService $jwtService
     * @param RoleService $roleService
     * @param ResponseEntity $re
     * @param TokenUserProvider $userProvider
     */
    public function __construct(
        JwtService $jwtService,
        RoleService $roleService,
        ResponseEntity $re,
        TokenUserProvider $userProvider
    )
    {
        $this->jwtService = $jwtService;
        $this->roleService = $roleService;
        $this->re = $re;
        $this->userProvider = $userProvider;
    }

    /**
     * @param RequestEvent $event
     * @return void
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $path = $request->getPathInfo();

        if ($this->isPersonalRoute($path) || $this->isAdminRoute($path)) {
            $token = $request->headers->get('Authorization');

            if (!$token || !preg_match('/Bearer\s(\S+)/', $token, $matches)) {
                $response = $this->re->withException(new UnauthorizedException('Token not provided.'));
                $event->setResponse($response);
                return;
            }

            $token = $matches[1];

            try {
                $decodedToken = $this->jwtService->decodeToken($token, JwtUsage::USAGE_API_ACCESS);
                $user = $this->userProvider->loadUserByIdentifier($decodedToken['user_id']);

                if (!$user instanceof Account) {
                    $response = $this->re->withException(new UnauthorizedException('User not found.'));
                    $event->setResponse($response);
                    return;
                }

                if (!$user->isActive()) {
                    $response = $this->re->withException(new ForbiddenException('User is not active. Please check your email for activation.'));
                    $event->setResponse($response);
                    return;
                }

                $request->attributes->set('account', $user);

                if ($this->isAdminRoute($path) && !$this->roleService->isAdmin($user)) {
                    $response = $this->re->withException(new UnauthorizedException('Unsufficient permissions.'));
                    $event->setResponse($response);
                }
            } catch (UnauthorizedException) {
                $response = $this->re->withException(new UnauthorizedException('Invalid or expired token.'));
                $event->setResponse($response);
            } catch (NotFoundException $e) {
                $response = $this->re->withException($e);
                $event->setResponse($response);
            }
        }
    }

    /**
     * @param string $path
     * @return bool
     */
    private function isPersonalRoute(string $path): bool
    {
        return str_starts_with($path, '/v1/personal/');
    }

    private function isAdminRoute(string $path): bool
    {
        return str_starts_with($path, '/v1/admin/');
    }
}
