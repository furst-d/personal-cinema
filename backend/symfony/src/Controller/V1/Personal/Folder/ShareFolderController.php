<?php

namespace App\Controller\V1\Personal\Folder;

use App\Controller\V1\Personal\BasePersonalController;
use App\DTO\Video\FolderQueryRequest;
use App\DTO\Video\FolderShareRequest;
use App\Exception\ApiException;
use App\Exception\BadRequestException;
use App\Exception\ForbiddenException;
use App\Helper\Jwt\JwtUsage;
use App\Service\Jwt\JwtService;
use App\Service\Locator\BaseControllerLocator;
use App\Service\Mailer\MailerService;
use App\Service\Video\FolderService;
use App\Service\Video\ShareService;
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

    /**
     * @param BaseControllerLocator $locator
     * @param FolderService $folderService
     * @param ShareService $shareService
     * @param JwtService $jwtService
     * @param MailerService $mailerService
     */
    public function __construct(
        BaseControllerLocator $locator,
        FolderService $folderService,
        ShareService $shareService,
        JwtService $jwtService,
        MailerService $mailerService
    )
    {
        parent::__construct($locator);
        $this->folderService = $folderService;
        $this->shareService = $shareService;
        $this->jwtService = $jwtService;
        $this->mailerService = $mailerService;
    }

    #[Route('', name: 'user_shared_folders', methods: ['GET'])]
    public function getSharedFolders(Request $request, FolderQueryRequest $folderQueryRequest): JsonResponse
    {
        try {
            $account = $this->getAccount($request);
            $parentFolderId = $folderQueryRequest->getParentId();

            $folderData = $this->folderService->getSharedFolderDataById($account, $parentFolderId);

            $folders = $this->shareService->getSharedFolders(
                $account,
                $folderData,
                $folderQueryRequest
            );

            return $this->re->withData($folders, ['folder:read']);
        } catch (ApiException $e) {
            return $this->re->withException($e);
        }
    }

    #[Route('', name: 'user_share_folder', methods: ['POST'])]
    public function share(Request $request, FolderShareRequest $shareRequest): JsonResponse
    {
        try {
            $account = $this->getAccount($request);

            if ($account->getEmail() === $shareRequest->email) {
                throw new ForbiddenException('You can not share folder with yourself');
            }

            $folder = $this->folderService->getAccountFolderById($account, $shareRequest->folderId);

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

    #[Route('/{id<\d+>}', name: 'user_delete_folder_share', methods: ['DELETE'])]
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
