<?php

namespace App\Service\Video;

use App\Entity\Account\Account;
use App\Entity\Video\Folder;
use App\Entity\Video\MD5;
use App\Entity\Video\Video;
use App\Exception\NotFoundException;
use App\Helper\Paginator\PaginatorResult;
use App\Repository\Video\MD5Repository;
use App\Repository\Video\VideoRepository;

class VideoService
{
    /**
     * @var VideoRepository $videoRepository
     */
    private VideoRepository $videoRepository;

    /**
     * @var MD5Repository $md5Repository
     */
    private MD5Repository $md5Repository;

    /**
     * @param VideoRepository $videoRepository
     * @param MD5Repository $md5Repository
     */
    public function __construct(
        VideoRepository $videoRepository,
        MD5Repository $md5Repository
    )
    {
        $this->videoRepository = $videoRepository;
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
     * @param Account|null $account
     * @param Folder|null $folder
     * @param int|null $limit
     * @param int|null $offset
     * @return PaginatorResult<Video>
     */
    public function getVideos(?Account $account, ?Folder $folder, ?int $limit, ?int $offset): PaginatorResult
    {
        return $this->videoRepository->findVideos($account, $folder, $limit, $offset);
    }

    /**
     * @param Video $video
     * @param string $name
     * @param Folder|null $folder
     * @return void
     */
    public function updateVideo(Video $video, string $name, ?Folder $folder): void
    {
        $video->setName($name);
        $video->setFolder($folder);
        $this->videoRepository->save($video);
    }

    /**
     * @param Video $video
     * @return void
     */
    public function deleteVideo(Video $video): void
    {
        $this->videoRepository->delete($video);
    }
}
