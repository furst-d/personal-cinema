<?php

namespace App\Service\Video;

use App\DTO\PaginatorRequest;
use App\Entity\Account\Account;
use App\Entity\Video\Folder;
use App\Entity\Video\Share\ShareFolder;
use App\Entity\Video\Share\ShareVideo;
use App\Entity\Video\Share\ShareVideoPublic;
use App\Entity\Video\Share\ShareVideoPublicView;
use App\Entity\Video\Video;
use App\Exception\ConflictException;
use App\Exception\ForbiddenException;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Helper\DTO\PaginatorResult;
use App\Helper\Generator\RandomGenerator;
use App\Repository\Settings\SettingsRepository;
use App\Repository\Video\FolderRepository;
use App\Repository\Video\Share\ShareFolderRepository;
use App\Repository\Video\Share\ShareVideoPublicRepository;
use App\Repository\Video\Share\ShareVideoPublicViewRepository;
use App\Repository\Video\Share\ShareVideoRepository;
use App\Repository\Video\VideoRepository;

class ShareService
{
    /**
     * @var ShareFolderRepository $shareFolderRepository
     */
    private ShareFolderRepository $shareFolderRepository;

    /**
     * @var ShareVideoPublicRepository $shareVideoPublicRepository
     */
    private ShareVideoPublicRepository $shareVideoPublicRepository;

    /**
     * @var ShareVideoPublicViewRepository $shareVideoPublicViewRepository
     */
    private ShareVideoPublicViewRepository $shareVideoPublicViewRepository;

    /**
     * @var ShareVideoRepository $shareVideoRepository
     */
    private ShareVideoRepository $shareVideoRepository;

    /**
     * @var SettingsRepository $settingsRepository
     */
    private SettingsRepository $settingsRepository;

    /**
     * @var VideoRepository $videoRepository
     */
    private VideoRepository $videoRepository;

    /**
     * @var FolderRepository $folderRepository
     */
    private FolderRepository $folderRepository;

    /**
     * @var RandomGenerator $generator
     */
    private RandomGenerator $generator;

    public const VIDEO_NOT_FOUND_MESSAGE = "Video not found";
    public const NO_PERMISSION_MESSAGE = "You don't have permission to see this video";
    public const NO_SHARE_VIDEO_WITH_YOURSELF_MESSAGE = "You can not share video with yourself";
    public const NO_SHARE_FOLDER_WITH_YOURSELF_MESSAGE = "You can not share folder with yourself";
    public const VIDEO_ALREADY_SHARED_MESSAGE = "Video is already shared with this user";
    public const FOLDER_ALREADY_SHARED_MESSAGE = "Folder is already shared with this user";
    public const CANNOT_CREATE_LINK_MESSAGE = "Cannot create another link until the previous one expires";
    public const VIDEO_SHARE_NOT_FOUND_MESSAGE = "Video share not found";
    public const FOLDER_SHARE_NOT_FOUND_MESSAGE = "Folder share not found";

    /**
     * @param ShareFolderRepository $shareFolderRepository
     * @param ShareVideoPublicRepository $shareVideoPublicRepository
     * @param ShareVideoPublicViewRepository $shareVideoPublicViewRepository
     * @param ShareVideoRepository $shareVideoRepository
     * @param SettingsRepository $settingsRepository
     * @param VideoRepository $videoRepository
     * @param FolderRepository $folderRepository
     * @param RandomGenerator $generator
     */
    public function __construct(
        ShareFolderRepository $shareFolderRepository,
        ShareVideoPublicRepository $shareVideoPublicRepository,
        ShareVideoPublicViewRepository $shareVideoPublicViewRepository,
        ShareVideoRepository $shareVideoRepository,
        SettingsRepository $settingsRepository,
        VideoRepository $videoRepository,
        FolderRepository $folderRepository,
        RandomGenerator $generator
    )
    {
        $this->shareFolderRepository = $shareFolderRepository;
        $this->shareVideoPublicRepository = $shareVideoPublicRepository;
        $this->shareVideoPublicViewRepository = $shareVideoPublicViewRepository;
        $this->shareVideoRepository = $shareVideoRepository;
        $this->settingsRepository = $settingsRepository;
        $this->videoRepository = $videoRepository;
        $this->folderRepository = $folderRepository;
        $this->generator = $generator;
    }

    /**
     * @param Account $account
     * @param Video $video
     * @return bool
     */
    public function hasSharedVideoAccess(Account $account, Video $video): bool
    {
        return $this->shareVideoRepository->hasSharedVideoAccess($account, $video);
    }

    /**
     * @param Account $account
     * @param Folder $folder
     * @return bool
     */
    public function hasSharedFolderAccess(Account $account, Folder $folder): bool
    {
        return $this->shareFolderRepository->hasSharedFolderAccess($account, $folder);
    }

    /**
     * @param Account $account
     * @param PaginatorRequest $paginatorRequest
     * @return PaginatorResult<Folder>
     */
    public function getSharedFolders(Account $account, PaginatorRequest $paginatorRequest): PaginatorResult
    {
        return $this->folderRepository->findSharedFolders($account, $paginatorRequest);
    }

    /**
     * @param Account $account
     * @param PaginatorRequest $paginatorRequest
     * @return PaginatorResult<Video>
     */
    public function getSharedVideos(Account $account, PaginatorRequest $paginatorRequest): PaginatorResult
    {
        return $this->videoRepository->findSharedVideos($account, $paginatorRequest);
    }

    /**
     * @param Account $account
     * @param Video $video
     * @return ShareVideo
     * @throws ConflictException
     */
    public function createVideoShare(Account $account, Video $video): ShareVideo
    {
        if ($this->isVideoAlreadyShared($account, $video)) {
            throw new ConflictException(self::VIDEO_ALREADY_SHARED_MESSAGE);
        }

        $shareVideo = new ShareVideo($video, $account);
        $this->shareVideoRepository->save($shareVideo);
        return $shareVideo;
    }

    /**
     * @param Account $account
     * @param Folder $folder
     * @return ShareFolder
     * @throws ConflictException
     */
    public function createFolderShare(Account $account, Folder $folder): ShareFolder
    {
        if ($this->isFolderAlreadyShared($account, $folder)) {
            throw new ConflictException(self::FOLDER_ALREADY_SHARED_MESSAGE);
        }

        $shareFolder = new ShareFolder($folder, $account);
        $this->shareFolderRepository->save($shareFolder);
        return $shareFolder;
    }

    /**
     * @param Video $video
     * @return ShareVideoPublic
     * @throws ForbiddenException|InternalException
     */
    public function createPublicVideoShareLink(Video $video): ShareVideoPublic
    {
        if ($this->shareVideoPublicRepository->findValidByVideo($video)) {
            throw new ForbiddenException(self::CANNOT_CREATE_LINK_MESSAGE);
        }

        $hash = $this->generator->generateString(64);
        $shareVideoPublic = new ShareVideoPublic($video, $hash);

        $this->shareVideoPublicRepository->save($shareVideoPublic);
        return $shareVideoPublic;
    }

    /**
     * @param string $hash
     * @param string $sessionId
     * @return ShareVideoPublic
     * @throws NotFoundException|ForbiddenException
     */
    public function getPublicVideoByHash(string $hash, string $sessionId): ShareVideoPublic
    {
        $publicVideoShare = $this->shareVideoPublicRepository->findValidByHash($hash);

        $this->checkPublicAccess($publicVideoShare, $sessionId);

        return $publicVideoShare;
    }

    /**
     * @param Video $video
     * @param string $sessionId
     * @return void
     * @throws ForbiddenException
     * @throws NotFoundException
     */
    public function addView(Video $video, string $sessionId): void
    {
        $publicVideoShare = $this->shareVideoPublicRepository->findValidByVideo($video);

        if (!$publicVideoShare) {
            throw new NotFoundException(self::VIDEO_NOT_FOUND_MESSAGE);
        }

        if ($this->alreadySawPublicVideo($publicVideoShare, $sessionId)) {
            return;
        }

        if ($this->viewsLimitExceeded($publicVideoShare)) {
            throw new ForbiddenException(self::NO_PERMISSION_MESSAGE);
        }

        $view = new ShareVideoPublicView($publicVideoShare, $sessionId);
        $this->shareVideoPublicViewRepository->save($view);
    }

    /**
     * @param Account $account
     * @param int $id
     * @return ShareVideo
     * @throws NotFoundException
     */
    public function getAccountVideoShareById(Account $account, int $id): ShareVideo
    {
        $videoShare = $this->shareVideoRepository->findOneBy(['id' => $id]);

        if (!$videoShare || $videoShare->getVideo()->getAccount() !== $account) {
            throw new NotFoundException(self::VIDEO_SHARE_NOT_FOUND_MESSAGE);
        }

        return $videoShare;
    }

    /**
     * @param Account $account
     * @param int $id
     * @return ShareFolder
     * @throws NotFoundException
     */
    public function getAccountFolderShareById(Account $account, int $id): ShareFolder
    {
        $folderShare = $this->shareFolderRepository->findOneBy(['id' => $id]);

        if (!$folderShare || $folderShare->getFolder()->getOwner() !== $account) {
            throw new NotFoundException(self::FOLDER_SHARE_NOT_FOUND_MESSAGE);
        }

        return $folderShare;
    }

    /**
     * @param ShareVideo $videoShare
     * @return void
     */
    public function deleteVideoShare(ShareVideo $videoShare): void
    {
        $this->shareVideoRepository->delete($videoShare);
    }

    /**
     * @param ShareFolder $videoShare
     * @return void
     */
    public function deleteFolderShare(ShareFolder $videoShare): void
    {
        $this->shareFolderRepository->delete($videoShare);
    }

    /**
     * @param Account $account
     * @param Folder $folder
     * @param string $email
     * @return void
     * @throws ConflictException
     * @throws ForbiddenException
     */
    public function allowedToShareFolder(Account $account, Folder $folder, string $email): void
    {
        if ($account->getEmail() === $email) {
            throw new ForbiddenException(self::NO_SHARE_FOLDER_WITH_YOURSELF_MESSAGE);
        }

        if ($this->isFolderAlreadyShared($account, $folder)) {
            throw new ConflictException(self::FOLDER_ALREADY_SHARED_MESSAGE);
        }
    }

    /**
     * @param Account $account
     * @param Video $video
     * @param string $email
     * @return void
     * @throws ConflictException
     * @throws ForbiddenException
     */
    public function allowedToShareVideo(Account $account, Video $video, string $email): void
    {
        if ($account->getEmail() === $email) {
            throw new ForbiddenException(self::NO_SHARE_VIDEO_WITH_YOURSELF_MESSAGE);
        }

        if ($this->isVideoAlreadyShared($account, $video)) {
            throw new ConflictException(self::VIDEO_ALREADY_SHARED_MESSAGE);
        }
    }

    /**
     * @return int
     */
    public function getPublicLinkViewLimit(): int
    {
        return (int) $this->settingsRepository->getPublicLinkViewLimit();
    }

    private function alreadySawPublicVideo(ShareVideoPublic $publicVideoShare, string $sessionId): bool {
        return (bool) $this->shareVideoPublicViewRepository->findShareViews($publicVideoShare, $sessionId);
    }

    /**
     * @param ShareVideoPublic|null $publicVideoShare
     * @param string $sessionId
     * @return void
     * @throws ForbiddenException
     * @throws NotFoundException
     */
    private function checkPublicAccess(?ShareVideoPublic $publicVideoShare, string $sessionId): void
    {
        if (!$publicVideoShare) {
            throw new NotFoundException(self::VIDEO_NOT_FOUND_MESSAGE);
        }

        if (!$this->casSeePublicVideo($publicVideoShare, $sessionId)) {
            throw new ForbiddenException(self::NO_PERMISSION_MESSAGE);
        }
    }

    /**
     * @param Account $account
     * @param Video $video
     * @return bool
     */
    private function isVideoAlreadyShared(Account $account, Video $video): bool
    {
        return $this->shareVideoRepository->isVideoAlreadyShared($account, $video);
    }

    /**
     * @param Account $account
     * @param Folder $folder
     * @return bool
     */
    private function isFolderAlreadyShared(Account $account, Folder $folder): bool
    {
        return $this->shareFolderRepository->isFolderAlreadyShared($account, $folder);
    }

    /**
     * @param ShareVideoPublic $publicVideoShare
     * @param string $sessionId
     * @return bool
     */
    private function casSeePublicVideo(ShareVideoPublic $publicVideoShare, string $sessionId): bool
    {
        if ($this->alreadySawPublicVideo($publicVideoShare, $sessionId)) {
            return true;
        }

        return !$this->viewsLimitExceeded($publicVideoShare);
    }

    /**
     * @param ShareVideoPublic $publicVideoShare
     * @return bool
     */
    private function viewsLimitExceeded(ShareVideoPublic $publicVideoShare): bool
    {
        return count($publicVideoShare->getViews()) >= $this->getPublicLinkViewLimit();
    }
}
