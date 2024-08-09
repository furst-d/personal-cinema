<?php

namespace App\Service\Video;

use App\DTO\PaginatorRequest;
use App\Entity\Account\Account;
use App\Entity\Video\Folder;
use App\Entity\Video\Share\ShareVideo;
use App\Entity\Video\Video;
use App\Exception\ConflictException;
use App\Exception\NotFoundException;
use App\Helper\DTO\PaginatorResult;
use App\Helper\Video\FolderData;
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
     * @param ShareFolderRepository $shareFolderRepository
     * @param ShareVideoPublicRepository $shareVideoPublicRepository
     * @param ShareVideoPublicViewRepository $shareVideoPublicViewRepository
     * @param ShareVideoRepository $shareVideoRepository
     */
    public function __construct(
        ShareFolderRepository $shareFolderRepository,
        ShareVideoPublicRepository $shareVideoPublicRepository,
        ShareVideoPublicViewRepository $shareVideoPublicViewRepository,
        ShareVideoRepository $shareVideoRepository
    )
    {
        $this->shareFolderRepository = $shareFolderRepository;
        $this->shareVideoPublicRepository = $shareVideoPublicRepository;
        $this->shareVideoPublicViewRepository = $shareVideoPublicViewRepository;
        $this->shareVideoRepository = $shareVideoRepository;
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

    private function isVideoAlreadyShared(Account $account, Video $video): bool
    {
        return $this->shareVideoRepository->isVideoAlreadyShared($account, $video);
    }
}
