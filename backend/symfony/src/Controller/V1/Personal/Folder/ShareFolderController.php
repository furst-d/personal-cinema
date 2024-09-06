<?php

namespace App\Controller\V1\Personal\Folder;

use App\Attribute\OpenApi\Request\Query\QueryInt;
use App\Attribute\OpenApi\Request\Query\QueryLimit;
use App\Attribute\OpenApi\Request\Query\QueryOffset;
use App\Attribute\OpenApi\Request\Query\QueryOrderBy;
use App\Attribute\OpenApi\Request\Query\QuerySortBy;
use App\Attribute\OpenApi\Request\RequestBody;
use App\Attribute\OpenApi\Response\ResponseData;
use App\Attribute\OpenApi\Response\ResponseError;
use App\Attribute\OpenApi\Response\ResponseMessage;
use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\Account\TokenRequest;
use App\DTO\Video\FolderQueryRequest;
use App\DTO\Video\FolderShareRequest;
use App\Entity\Video\Folder;
use App\Exception\ApiException;
use App\Exception\BadGatewayException;
use App\Exception\BadRequestException;
use App\Exception\ConflictException;
use App\Exception\ForbiddenException;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use App\Helper\DTO\SortBy;
use App\Helper\Jwt\JwtUsage;
use App\Helper\Regex\RegexRoute;
use App\Service\Account\AccountService;
use App\Service\Jwt\JwtService;
use App\Service\Mailer\MailerService;
use App\Service\Video\FolderService;
use App\Service\Video\ShareService;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/personal/folders/share')]
class ShareFolderController extends BasePersonalController
{
    /**
     * @var FolderService $folderService
     */
    private FolderService $folderService;

    /**
     * @var ShareService $shareService
     */
    private ShareService $shareService;

    /**
     * @var JwtService $jwtService
     */
    private JwtService $jwtService;

    /**
     * @var MailerService $mailerService
     */
    private MailerService $mailerService;

    /**+
     * @var AccountService $accountService
     */
    private AccountService $accountService;

    /**
     * @param FolderService $folderService
     * @param ShareService $shareService
     * @param JwtService $jwtService
     * @param MailerService $mailerService
     * @param AccountService $accountService
     */
    public function __construct(
        FolderService $folderService,
        ShareService $shareService,
        JwtService $jwtService,
        MailerService $mailerService,
        AccountService $accountService
    )
    {
        $this->folderService = $folderService;
        $this->shareService = $shareService;
        $this->jwtService = $jwtService;
        $this->mailerService = $mailerService;
        $this->accountService = $accountService;
    }

    #[OA\Get(
        description: "Retrieve a list of folders that was shared with the user",
        summary: "List of folders shared with the user",
        tags: [FolderController::TAG],
    )]
    #[QueryInt(name: 'parentId', description: "Folder parent ID")]
    #[QueryLimit]
    #[QueryOffset]
    #[QuerySortBy(choices: [SortBy::NAME, SortBy::UPDATE_DATE], default: SortBy::NAME)]
    #[QueryOrderBy]
    #[ResponseData(entityClass: Folder::class, groups: [Folder::FOLDER_READ], pagination: true, description: "List of folders")]
    #[ResponseError(exception: new BadRequestException())]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
    #[Route('', name: 'user_shared_folders', methods: ['GET'])]
    public function getSharedFolders(Request $request, FolderQueryRequest $folderQueryRequest): JsonResponse
    {
        $account = $this->getAccount($request);

        $folders = $this->shareService->getSharedFolders(
            $account,
            $folderQueryRequest
        );

        return $this->re->withData($folders, [Folder::FOLDER_READ]);
    }

    #[Route('', name: 'user_share_folder', methods: ['POST'])]
    #[OA\Post(
        description: "Share a folder with another user",
        summary: "Share a folder",
        requestBody: new RequestBody(entityClass: FolderShareRequest::class),
        tags: [FolderController::TAG],
    )]
    #[ResponseMessage(message: "Folder share request was send to the target email")]
    #[ResponseError(exception: new BadRequestException())]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new ForbiddenException(ShareService::NO_SHARE_FOLDER_WITH_YOURSELF_MESSAGE))]
    #[ResponseError(exception: new NotFoundException(FolderService::NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new ConflictException(ShareService::FOLDER_ALREADY_SHARED_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[ResponseError(exception: new BadGatewayException())]
    #[Security(name: 'Bearer')]
    public function share(Request $request, FolderShareRequest $shareRequest): JsonResponse
    {
        try {
            $account = $this->getAccount($request);

            $folder = $this->folderService->getAccountFolderById($account, $shareRequest->folderId);
            $this->shareService->allowedToShareFolder($account, $folder, $shareRequest->email);

            $token = $this->jwtService->generateToken($account, JwtUsage::USAGE_SHARE_FOLDER, [
                'target_email' => $shareRequest->email,
                'folder_id' => $folder->getId(),
            ]);

            $this->mailerService->sendShareItem(
                $shareRequest->email,
                $folder->getName(),
                $account->getEmail(),
                $token,
                true
            );

            return $this->re->withMessage('Folder share request was send to the target email');
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[OA\Post(
        description: "Accept a folder share request",
        summary: "Accept a folder share",
        requestBody: new RequestBody(entityClass: TokenRequest::class),
        tags: [FolderController::TAG],
    )]
    #[ResponseData(entityClass: Folder::class, groups: [Folder::FOLDER_READ], collection: false, description: "Shared folder")]
    #[ResponseError(exception: new BadRequestException())]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException())]
    #[ResponseError(exception: new ConflictException(ShareService::FOLDER_ALREADY_SHARED_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
    #[Route('/accept', name: 'user_accept_share_folder', methods: ['POST'])]
    public function acceptShare(Request $request, TokenRequest $tokenRequest): JsonResponse
    {
        try {
            $account = $this->getAccount($request);
            $decodedToken = $this->jwtService->decodeToken($tokenRequest->token, JwtUsage::USAGE_SHARE_FOLDER);

            if ($account !== $this->accountService->getAccountByEmail($decodedToken['target_email'])) {
                throw new BadRequestException('Invalid token.');
            }

            $folder = $this->folderService->getFolderById($decodedToken['folder_id']);
            $this->shareService->createFolderShare($account, $folder);

            return $this->re->withData($folder, [Folder::FOLDER_READ]);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[OA\Delete(
        description: "Delete a folder share.",
        summary: "Delete a folder share",
        tags: [FolderController::TAG],
    )]
    #[ResponseMessage(message: "Folder share deleted.")]
    #[ResponseError(exception: new UnauthorizedException())]
    #[ResponseError(exception: new NotFoundException(ShareService::FOLDER_SHARE_NOT_FOUND_MESSAGE))]
    #[ResponseError(exception: new InternalException())]
    #[Security(name: 'Bearer')]
    #[Route(RegexRoute::ID, name: 'user_delete_folder_share', methods: ['DELETE'])]
    public function deleteFolderShare(Request $request, int $id): JsonResponse
    {
        try {
            $account = $this->getAccount($request);

            $videoShare = $this->shareService->getAccountFolderShareById($account, $id);
            $this->shareService->deleteFolderShare($videoShare);

            return $this->re->withMessage('Folder share deleted.');
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }
}
