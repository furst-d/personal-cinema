<?php

namespace App\Service\Account;

use Symfony\Component\HttpFoundation\Request;

class SessionService
{
    /**
     * Generate a session token
     * @param Request $request
     * @return string
     */
    public function generate(Request $request): string
    {
        $userAgent = $request->headers->get('User-Agent');
        $ipAddress = $request->getClientIp();

        return hash('sha256', $userAgent . $ipAddress);
    }
}
