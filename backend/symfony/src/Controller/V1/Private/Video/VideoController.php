<?php

namespace App\Controller\V1\Private\Video;

use App\Controller\V1\Private\BasePrivateController;
use App\DTO\Video\UploadRequest;
use App\Exception\InternalException;
use App\Helper\Jwt\JwtUsage;
use App\Service\Cdn\CdnService;
use App\Service\Jwt\JwtService;
use App\Service\Locator\BaseControllerLocator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/private/videos')]
class VideoController extends BasePrivateController
{
    /**
     * @var JwtService $jwtService
     */
    private JwtService $jwtService;

    /**
     * @var CdnService $cdnService
     */
    private CdnService $cdnService;

    /**
     * @param BaseControllerLocator $locator
     * @param JwtService $jwtService
     * @param CdnService $cdnService
     */
    public function __construct(
        BaseControllerLocator $locator,
        JwtService $jwtService,
        CdnService $cdnService
    )
    {
        parent::__construct($locator);
        $this->jwtService = $jwtService;
        $this->cdnService = $cdnService;
    }


    #[Route('/upload', name: 'user_video_upload', methods: ['POST'])]
    public function createUploadLink(Request $request, UploadRequest $uploadRequest): JsonResponse
    {
        try {
            $account = $this->getAccount($request);

            $videoToken = $this->jwtService->generateToken($account, JwtUsage::USAGE_UPLOAD, [
                'name' => $uploadRequest->name,
                'folder' => $uploadRequest->folderId,
            ]);

            $data = $this->cdnService->createUploadData([
                'params' => [
                    'video_token' => $videoToken,
                ]
            ]);

            return $this->re->withData($data);
        } catch (InternalException $e) {
            return $this->re->withException($e);
        }
    }
}
