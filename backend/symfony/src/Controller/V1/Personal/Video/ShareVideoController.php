<?php

namespace App\Controller\V1\Personal\Video;

use App\Attribute\OpenApi\Request\RequestBody;
use App\Attribute\OpenApi\Response\ResponseData;
use App\Attribute\OpenApi\Response\ResponseError;
use App\Attribute\OpenApi\Response\ResponseMessage;
use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\Account\TokenRequest;
use App\DTO\Video\VideoPublicShareRequest;
use App\DTO\Video\VideoQueryRequest;
use App\DTO\Video\VideoShareRequest;
use App\Entity\Video\Share\ShareVideoPublic;
use App\Entity\Video\Video;
use App\Exception\ApiException;
use App\Exception\BadGatewayException;
use App\Exception\BadRequestException;
use App\Exception\ConflictException;
use App\Exception\ForbiddenException;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use App\Helper\Jwt\JwtUsage;
use App\Helper\Regex\RegexRoute;
use App\Service\Account\AccountService;
use App\Service\Jwt\JwtService;
use App\Service\Locator\BaseControllerLocator;
use App\Service\Mailer\MailerService;
use App\Service\Video\ShareService;
use App\Service\Video\VideoService;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/personal/videos/share')]
class ShareVideoController extends BasePersonalController
{
    /**
     * @var JwtService $jwtService
     */
    private JwtService $jwtService;

    /**
     * @var VideoService $videoService
     */
    private VideoService $videoService;

    /**
     * @var ShareService $shareService
     */
    private ShareService $shareService;

    /**
     * @var AccountService $accountService
     */
    private AccountService $accountService;

    /**
     * @var MailerService $mailerService
     */
    private MailerService $mailerService;

    /**
     * @param BaseControllerLocator $locator
     * @param JwtService $jwtService
     * @param VideoService $videoService
     * @param ShareService $shareService
     * @param AccountService $accountService
     * @param MailerService $mailerService
     */
    public function __construct(
        BaseControllerLocator $locator,
        JwtService $jwtService,
        VideoService $videoService,
        ShareService $shareService,
        AccountService $accountService,
        MailerService $mailerService
    )
    {
        parent::__construct($locator);
        $this->jwtService = $jwtService;
        $this->videoService = $videoService;
        $this->shareService = $shareService;
        $this->accountService = $accountService;
        $this->mailerService = $mailerService;
    }

    #[OA\Get(
        description: "Retrieve a list of videos that was shared with the user",
        summary: "List of videos shared with the user",
        tags: [VideoController::TAG],
    )]
    #[ResponseData(entityClass: Video::class, groups: [Video::VIDEOS_READ], pagination: true, description: "List of videos")]
    #[ResponseError(exception: new BadRequestException())]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
    #[Route('', name: 'user_shared_videos', methods: ['GET'])]
    public function getSharedVideos(Request $request, #[MapQueryString] VideoQueryRequest $videoQueryRequest): JsonResponse
    {
        try {
            $account = $this->getAccount($request);

            $videos = $this->shareService->getSharedVideos(
                $account,
                $videoQueryRequest
            );

            $this->videoService->addThumbnailToVideos($videos->getData(), $account);

            return $this->re->withData($videos, [Video::VIDEOS_READ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[OA\Post(
        description: "Share a video with another user",
        summary: "Share a video",
        requestBody: new RequestBody(entityClass: VideoShareRequest::class),
        tags: [VideoController::TAG],
    )]
    #[ResponseMessage(message: "Video share request was send to the target email")]
    #[ResponseError(exception: new BadRequestException())]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new ForbiddenException(ShareService::NO_SHARE_VIDEO_WITH_YOURSELF_MESSAGE))]
    #[ResponseError(exception: new NotFoundException(ShareService::VIDEO_NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new ConflictException(ShareService::VIDEO_ALREADY_SHARED_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[ResponseError(exception: new BadGatewayException("Failed to send email"))]
    #[Security(name: 'Bearer')]
    #[Route('', name: 'user_share_video', methods: ['POST'])]
    public function share(Request $request, VideoShareRequest $shareRequest): JsonResponse
    {
        try {
            $account = $this->getAccount($request);

            $video = $this->videoService->getAccountVideoById($account, $shareRequest->videoId);
            $this->shareService->allowedToShareVideo($account, $video, $shareRequest->email);

            $token = $this->jwtService->generateToken($account, JwtUsage::USAGE_SHARE_VIDEO, [
                'target_email' => $shareRequest->email,
                'video_id' => $video->getId(),
            ]);

            $this->mailerService->sendShareItem(
                $shareRequest->email,
                $video->getName(),
                $account->getEmail(),
                $token,
                false
            );

            return $this->re->withMessage('Video share request was send to the target email');
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[OA\Post(
        description: "Accept a video share request",
        summary: "Accept a video share",
        requestBody: new RequestBody(entityClass: TokenRequest::class),
        tags: [VideoController::TAG],
    )]
    #[ResponseData(entityClass: Video::class, groups: [Video::VIDEOS_READ], description: "Shared video")]
    #[ResponseError(exception: new BadRequestException())]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException())]
    #[ResponseError(exception: new ConflictException(ShareService::VIDEO_ALREADY_SHARED_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
    #[Route('/accept', name: 'user_accept_share_video', methods: ['POST'])]
    public function acceptShare(Request $request, TokenRequest $tokenRequest): JsonResponse
    {
        try {
            $account = $this->getAccount($request);
            $decodedToken = $this->jwtService->decodeToken($tokenRequest->token, JwtUsage::USAGE_SHARE_VIDEO);

            if ($account !== $this->accountService->getAccountByEmail($decodedToken['target_email'])) {
                throw new BadRequestException('Invalid token.');
            }

            $video = $this->videoService->getVideoById($decodedToken['video_id']);
            $this->shareService->createVideoShare($account, $video);

            return $this->re->withData($video, [Video::VIDEOS_READ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[OA\Post(
        description: "Create a public link to share a video",
        summary: "Create a public link to share a video",
        requestBody: new RequestBody(entityClass: VideoPublicShareRequest::class),
        tags: [VideoController::TAG],
    )]
    #[ResponseData(entityClass: ShareVideoPublic::class, groups: [ShareVideoPublic::VIDEO_SHARED_PUBLIC_READ], description: "Shared video public link")]
    #[ResponseError(exception: new BadRequestException())]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new ForbiddenException(ShareService::CANNOT_CREATE_LINK_MESSAGE))]
    #[ResponseError(exception: new NotFoundException(VideoService::NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
    #[Route('/public', name: 'user_create_public_link', methods: ['POST'])]
    public function createPublicLink(Request $request, VideoPublicShareRequest $shareRequest): JsonResponse
    {
        try {
            $account = $this->getAccount($request);

            $video = $this->videoService->getAccountVideoById($account, $shareRequest->videoId);
            $sharedPublic = $this->shareService->createPublicVideoShareLink($video);

            return $this->re->withData($sharedPublic, [ShareVideoPublic::VIDEO_SHARED_PUBLIC_READ], Response::HTTP_CREATED);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[OA\Delete(
        description: "Delete a video share.",
        summary: "Delete a video share",
        tags: [VideoController::TAG],
    )]
    #[ResponseMessage(message: "Video share deleted.")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(ShareService::VIDEO_SHARE_NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
    #[Route(RegexRoute::ID, name: 'user_delete_video_share', methods: ['DELETE'])]
    public function deleteVideoShare(Request $request, int $id): JsonResponse
    {
        try {
            $account = $this->getAccount($request);

            $videoShare = $this->shareService->getAccountVideoShareById($account, $id);
            $this->shareService->deleteVideoShare($videoShare);

            return $this->re->withMessage('Video share deleted.');
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }
}
