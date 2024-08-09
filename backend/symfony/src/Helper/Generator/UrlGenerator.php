<?php

namespace App\Helper\Generator;

use App\Entity\Account\Account;
use App\Entity\Video\Video;
use App\Exception\InternalException;
use App\Helper\Jwt\JwtUsage;
use App\Service\Jwt\JwtService;

class UrlGenerator
{
    /**
     * @var string $backendUrl
     */
    private string $backendUrl;

    /**
     * @var JwtService $jwtService
     */
    private JwtService $jwtService;

    /**
     * @param string $backendUrl
     * @param JwtService $jwtService
     */
    public function __construct(string $backendUrl, JwtService $jwtService)
    {
        $this->backendUrl = $backendUrl;
        $this->jwtService = $jwtService;
    }

    /**
     * @param Account $account
     * @param Video $video
     * @return string
     * @throws InternalException
     */
    public function generateThumbnail(Account $account, Video $video): string
    {
        $token = $this->jwtService->generateToken($account, JwtUsage::USAGE_VIDEO_ACCESS, [
            'video_id' => $video->getId(),
        ]);

        return "$this->backendUrl/v1/private/videos/thumbnail?token=$token";
    }

    /**
     * @param Account $account
     * @param Video $video
     * @return string
     * @throws InternalException
     */
    public function generateVideo(Account $account, Video $video): string
    {
        $token = $this->jwtService->generateToken($account, JwtUsage::USAGE_VIDEO_ACCESS, [
            'video_id' => $video->getId(),
        ]);

        return "$this->backendUrl/v1/private/videos/url?token=$token";
    }

    /**
     * @param Video $video
     * @return string
     * @throws InternalException
     */
    public function generatePublicVideo(Video $video): string
    {
        $token = $this->jwtService->generateToken(null, JwtUsage::USAGE_PUBLIC_VIDEO_ACCESS, [
            'video_id' => $video->getId(),
        ]);

        return "$this->backendUrl/v1/share/url?token=$token";
    }
}
