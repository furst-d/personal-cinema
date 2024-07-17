<?php

namespace App\Service\Auth;

use App\Entity\Video\Video;
use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use App\Helper\Jwt\JwtUsage;
use App\Service\Account\AccountService;
use App\Service\Cdn\CdnService;
use App\Service\Jwt\JwtService;
use App\Service\Video\VideoService;
use Symfony\Component\HttpFoundation\Request;

class AuthService
{
    /**
     * @var CdnService $cdnService
     */
    private CdnService $cdnService;

    /**
     * @var VideoService $videoService
     */
    private VideoService $videoService;

    /**
     * @var JwtService $jwtService
     */
    private JwtService $jwtService;

    /**
     * @param CdnService $cdnService
     * @param VideoService $videoService
     * @param JwtService $jwtService
     */
    public function __construct(
        CdnService $cdnService,
        VideoService $videoService,
        JwtService $jwtService
    )
    {
        $this->cdnService = $cdnService;
        $this->videoService = $videoService;
        $this->jwtService = $jwtService;
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

    /**
     * @param Request $request
     * @return Video
     * @throws UnauthorizedException
     * @throws NotFoundException
     */
    public function authVideo(Request $request): Video
    {
        $token = $request->query->get('token');
        if (!$token) {
            throw new UnauthorizedException('Token is required');
        }

        $decodedToken = $this->jwtService->decodeToken($token, JwtUsage::USAGE_VIDEO_ACCESS);
        $videoId = $decodedToken['video_id'] ?? null;

        return $this->videoService->getVideoById($videoId);
    }
}
