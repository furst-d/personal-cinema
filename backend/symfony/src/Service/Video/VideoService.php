<?php

namespace App\Service\Video;

use App\DTO\PaginatorRequest;
use App\Entity\Account\Account;
use App\Entity\Video\Folder;
use App\Entity\Video\MD5;
use App\Entity\Video\Video;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Helper\Generator\UrlGenerator;
use App\Helper\DTO\PaginatorResult;
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
     * @var UrlGenerator $urlGenerator
     */
    private UrlGenerator $urlGenerator;

    private const NOT_FOUND_MESSAGE = 'Video not found';

    /**
     * @param VideoRepository $videoRepository
     * @param MD5Repository $md5Repository
     * @param UrlGenerator $urlGenerator
     */
    public function __construct(
        VideoRepository $videoRepository,
        MD5Repository $md5Repository,
        UrlGenerator $urlGenerator
    )
    {
        $this->videoRepository = $videoRepository;
        $this->md5Repository = $md5Repository;
        $this->urlGenerator = $urlGenerator;
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
            throw new NotFoundException(self::NOT_FOUND_MESSAGE);
        }

        return $video;
    }

    /**
     * @param Account $account
     * @param string $hash
     * @return Video
     * @throws NotFoundException
     */
    public function getAccountVideoByHash(Account $account, string $hash): Video
    {
        $video = $this->videoRepository->findOneBy(['account' => $account, 'hash' => $hash]);

        if (!$video) {
            throw new NotFoundException(self::NOT_FOUND_MESSAGE);
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
            throw new NotFoundException(self::NOT_FOUND_MESSAGE);
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
     * @param PaginatorRequest $paginatorRequest
     * @return PaginatorResult<Video>
     */
    public function getVideos(?Account $account, ?Folder $folder, PaginatorRequest $paginatorRequest): PaginatorResult
    {
        return $this->videoRepository->findVideos($account, $folder, $paginatorRequest);
    }

    /**
     * @param Video $video
     * @param PaginatorRequest $paginatorRequest
     * @return PaginatorResult
     */
    public function getVideoRecommendations(Video $video, PaginatorRequest $paginatorRequest): PaginatorResult
    {
        return $this->videoRepository->findRecommendations($video, $paginatorRequest);
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

    /**
     * @param Video[] $videos
     * @param Account $account
     * @return void
     * @throws InternalException
     */
    public function addThumbnailToVideos(array $videos, Account $account): void
    {
        foreach ($videos as $video) {
            $this->addThumbnailToVideo($video, $account);
        }
    }

    /**
     * @param Video $video
     * @param Account $account
     * @return void
     * @throws InternalException
     */
    public function addThumbnailToVideo(Video $video, Account $account): void
    {
        if ($video->getThumbnail()) {
            $video->setThumbnailUrl($this->urlGenerator->generateThumbnail($account, $video));
        }
    }

    /**
     * @param Video $video
     * @param Account $account
     * @return void
     * @throws InternalException
     */
    public function addVideoUrlToVideo(Video $video, Account $account): void
    {
        if ($video->getPath()) {
            $video->setVideoUrl($this->urlGenerator->generateVideo($account, $video));
        }
    }
}
