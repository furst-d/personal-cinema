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
use App\Helper\Video\FolderData;
use App\Repository\Settings\SettingsRepository;
use App\Repository\Video\Share\ShareFolderRepository;
use App\Repository\Video\Share\ShareVideoPublicRepository;
use App\Repository\Video\Share\ShareVideoPublicViewRepository;
use App\Repository\Video\Share\ShareVideoRepository;

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
     * @var RandomGenerator $generator
     */
    private RandomGenerator $generator;

    private const VIDEO_NOT_FOUND_MESSAGE = "Video not found";
    private const NO_PERMISSION_MESSAGE = "You don't have permission to see this video";

    /**
     * @param ShareFolderRepository $shareFolderRepository
     * @param ShareVideoPublicRepository $shareVideoPublicRepository
     * @param ShareVideoPublicViewRepository $shareVideoPublicViewRepository
     * @param ShareVideoRepository $shareVideoRepository
     * @param SettingsRepository $settingsRepository
     * @param RandomGenerator $generator
     */
    public function __construct(
        ShareFolderRepository $shareFolderRepository,
        ShareVideoPublicRepository $shareVideoPublicRepository,
        ShareVideoPublicViewRepository $shareVideoPublicViewRepository,
        ShareVideoRepository $shareVideoRepository,
        SettingsRepository $settingsRepository,
        RandomGenerator $generator
    )
    {
        $this->shareFolderRepository = $shareFolderRepository;
        $this->shareVideoPublicRepository = $shareVideoPublicRepository;
        $this->shareVideoPublicViewRepository = $shareVideoPublicViewRepository;
        $this->shareVideoRepository = $shareVideoRepository;
        $this->settingsRepository = $settingsRepository;
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
     * @param FolderData $folderData
     * @param PaginatorRequest $paginatorRequest
     * @return PaginatorResult<Folder>
     */
    public function getSharedFolders(Account $account, FolderData $folderData, PaginatorRequest $paginatorRequest): PaginatorResult
    {
        return $this->shareFolderRepository->findSharedFolders($account, $folderData, $paginatorRequest);
    }

    /**
     * @param Account $account
     * @param FolderData $folderData
     * @param PaginatorRequest $paginatorRequest
     * @return PaginatorResult<Video>
     */
    public function getSharedVideos(Account $account, FolderData $folderData, PaginatorRequest $paginatorRequest): PaginatorResult
    {
        return $this->shareVideoRepository->findSharedVideos($account, $folderData, $paginatorRequest);
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
            throw new ConflictException("Video is already shared with this user");
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
            throw new ConflictException("Folder is already shared with this user");
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
            throw new ForbiddenException("Cannot create another link until the previous one expires");
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
        $videoShare = $this->shareVideoRepository->findOneBy(['account' => $account, 'id' => $id]);

        if (!$videoShare) {
            throw new NotFoundException("Video share not found");
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
        $folderShare = $this->shareFolderRepository->findOneBy(['account' => $account, 'id' => $id]);

        if (!$folderShare) {
            throw new NotFoundException("Folder share not found");
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
            throw new ForbiddenException('You can not share folder with yourself');
        }

        if ($this->isFolderAlreadyShared($account, $folder)) {
            throw new ConflictException("Folder is already shared with this user");
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
            throw new ForbiddenException('You can not share video with yourself');
        }

        if ($this->isVideoAlreadyShared($account, $video)) {
            throw new ConflictException("Video is already shared with this user");
        }
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

    /**
     * @return int
     */
    private function getPublicLinkViewLimit(): int
    {
        return (int) $this->settingsRepository->getPublicLinkViewLimit();
    }
}
