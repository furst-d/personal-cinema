<?php

namespace App\Service\Video;

use App\DTO\Filter\FilterRequest;
use App\DTO\PaginatorRequest;
use App\Entity\Account\Account;
use App\Entity\Video\Folder;
use App\Entity\Video\MD5;
use App\Entity\Video\Video;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Helper\DTO\PaginatorResult;
use App\Helper\Generator\UrlGenerator;
use App\Helper\Video\FolderData;
use App\Helper\Video\NameNormalizer;
use App\Helper\Video\ThirdParty;
use App\Repository\Video\MD5Repository;
use App\Repository\Video\VideoRepository;
use App\Service\Cdn\CdnDeletionService;

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

    /**
     * @var ShareService $shareService
     */
    private ShareService $shareService;

    /**
     * @var FolderService $folderService
     */
    private FolderService $folderService;

    /**
     * @var CdnDeletionService $cdnService
     */
    private CdnDeletionService $cdnDeletionService;

    public const NOT_FOUND_MESSAGE = 'Video not found';

    /**
     * @param VideoRepository $videoRepository
     * @param MD5Repository $md5Repository
     * @param UrlGenerator $urlGenerator
     * @param ShareService $shareService
     * @param FolderService $folderService
     * @param CdnDeletionService $cdnDeletionService
     */
    public function __construct(
        VideoRepository      $videoRepository,
        MD5Repository        $md5Repository,
        UrlGenerator         $urlGenerator,
        ShareService         $shareService,
        FolderService        $folderService,
        CdnDeletionService   $cdnDeletionService
    )
    {
        $this->videoRepository = $videoRepository;
        $this->md5Repository = $md5Repository;
        $this->urlGenerator = $urlGenerator;
        $this->shareService = $shareService;
        $this->folderService = $folderService;
        $this->cdnDeletionService = $cdnDeletionService;
    }

    /**
     * @param string $cdnId
     * @return Video
     * @throws NotFoundException
     */
    public function getVideoByCdnId(string $cdnId): Video
    {
        $video = $this->videoRepository->findOneBy(['cdnId' => $cdnId]);

        if (!$video) {
            throw new NotFoundException(self::NOT_FOUND_MESSAGE);
        }

        return $video;
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
        $video = $this->videoRepository->findOneBy(['hash' => $hash]);

        if (!$video || ($video->getAccount() !== $account
                && !$this->shareService->hasSharedVideoAccess($account, $video))
                && !$this->folderService->hasUserAccessToFolder($account, $video->getFolder())) {
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
     * @param FolderData $folderData
     * @param PaginatorRequest $paginatorRequest
     * @param FilterRequest|null $filter
     * @return PaginatorResult<Video>
     */
    public function getVideos(?Account $account, FolderData $folderData, PaginatorRequest $paginatorRequest, ?FilterRequest $filter = null): PaginatorResult
    {
        return $this->videoRepository->findVideos($account, $folderData, $paginatorRequest, $filter);
    }

    /**
     * @param Account $account
     * @param string $phrase
     * @param PaginatorRequest $paginatorRequest
     * @return PaginatorResult<Video>
     */
    public function searchVideos(Account $account, string $phrase, PaginatorRequest $paginatorRequest): PaginatorResult
    {
        $phrase = NameNormalizer::normalize($phrase);
        return $this->videoRepository->searchVideos($account, $phrase, $paginatorRequest);
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
     * @param Video[] $videos
     * @param array $thirdParties
     * @return void
     */
    public function deleteVideos(array $videos, array $thirdParties = []): void
    {
        $this->deleteThirdParties($videos, $thirdParties);

        foreach ($videos as $video) {
            $this->deleteVideo($video);
        }
    }

    /**
     * @param Video $video
     * @return void
     */
    public function deleteVideo(Video $video): void
    {
        if ($size = $video->getSize()) {
            $storage = $video->getAccount()->getStorage();
            $storage->setUsedStorage($storage->getUsedStorage() - $size);
        }

        $this->videoRepository->delete($video);
    }

    /**
     * @param Video[] $videos
     * @param ThirdParty[] $thirdParties
     * @return void
     */
    private function deleteThirdParties(array $videos, array $thirdParties): void
    {
        if (in_array(ThirdParty::CDN, $thirdParties)) {
            $this->cdnDeletionService->batchDelete($videos);
        }
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
        if (!$video->getConversions()->isEmpty()) {
            $video->setVideoUrl($this->urlGenerator->generateVideo($account, $video));
        }
    }

    /**
     * @param Video $video
     * @return void
     * @throws InternalException
     */
    public function addPublicVideoUrlToVideo(Video $video): void
    {
        if (!$video->getConversions()->isEmpty()) {
            $video->setVideoUrl($this->urlGenerator->generatePublicVideo($video));
        }
    }

    /**
     * @param int[] $ids
     * @return Video[]
     * @throws NotFoundException
     */
    public function getVideosByIds(array $ids): array
    {
        $videos = $this->videoRepository->findByIds($ids);

        if (count($videos) !== count($ids)) {
            throw new NotFoundException("Some videos not found.");
        }

        return $videos;
    }
}
