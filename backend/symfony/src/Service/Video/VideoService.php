<?php

namespace App\Service\Video;

use App\Entity\Account\Account;
use App\Entity\Video\Folder;
use App\Entity\Video\MD5;
use App\Entity\Video\Video;
use App\Exception\NotFoundException;
use App\Repository\Video\FolderRepository;
use App\Repository\Video\MD5Repository;
use App\Repository\Video\VideoRepository;

class VideoService
{
    /**
     * @var VideoRepository $videoRepository
     */
    private VideoRepository $videoRepository;

    /**
     * @var FolderRepository $folderRepository
     */
    private FolderRepository $folderRepository;

    /**
     * @var MD5Repository $md5Repository
     */
    private MD5Repository $md5Repository;

    /**
     * @param VideoRepository $videoRepository
     * @param FolderRepository $folderRepository
     * @param MD5Repository $md5Repository
     */
    public function __construct(
        VideoRepository $videoRepository,
        FolderRepository $folderRepository,
        MD5Repository $md5Repository
    )
    {
        $this->videoRepository = $videoRepository;
        $this->folderRepository = $folderRepository;
        $this->md5Repository = $md5Repository;
    }

    /**
     * @param string $cdnId
     * @return Video|null
     */
    public function getVideoByCdnId(string $cdnId): ?Video
    {
        return $this->videoRepository->findOneBy(['cdnId' => $cdnId]);
    }

    /**
     * @param Account $account
     * @param int $id
     * @return Video
     * @throws NotFoundException
     */
    public function getAccountVideoById(Account $account, int $id): Video
    {
        $video = $this->videoRepository->findOneBy(['account' => $account, 'id' => $id]);

        if (!$video) {
            throw new NotFoundException('Video not found');
        }

        return $video;
    }

    /**
     * @param int $videoId
     * @return Video
     * @throws NotFoundException
     */
    public function getVideoById(int $videoId): Video
    {
        $video = $this->videoRepository->find($videoId);

        if (!$video) {
            throw new NotFoundException('Video not found');
        }

        return $video;
    }

    /**
     * @param string $hash
     * @return MD5|null
     */
    public function getMd5ByHash(string $hash): ?MD5
    {
        return $this->md5Repository->findOneBy(['md5' => $hash]);
    }

    /**
     * @param int $id
     * @return Folder|null
     */
    public function getFolderById(int $id): ?Folder
    {
        return $this->folderRepository->find($id);
    }
}
