<?php

namespace App\Service\Auth;

use App\Exception\UnauthorizedException;
use App\Service\Cdn\CdnService;
use Symfony\Component\HttpFoundation\Request;

class AuthService
{
    /**
     * @var CdnService $cdnService
     */
    private CdnService $cdnService;

    /**
     * @param CdnService $cdnService
     */
    public function __construct(CdnService $cdnService)
    {
        $this->cdnService = $cdnService;
    }

    /**
     * @param Request $request
     * @return void
     * @throws UnauthorizedException
     */
    public function authCdn(Request $request): void
    {
        $token = $request->query->get('token');
        if (!$token) {
            throw new UnauthorizedException('Token is required');
        }

        $callbackKey = $this->cdnService->getCdnCallbackKey();
        if ($token !== $callbackKey) {
            throw new UnauthorizedException('Invalid token');
        }
    }
}
