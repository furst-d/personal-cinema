<?php

namespace App\Helper\Cdn;

use App\DTO\Video\CdnVideoRequest;
use App\Entity\Video\MD5;
use App\Entity\Video\Video;
use App\Exception\BadRequestException;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use App\Helper\Jwt\JwtUsage;
use App\Service\Account\AccountService;
use App\Service\Jwt\JwtService;
use App\Service\Video\FolderService;
use App\Service\Video\VideoService;
use Doctrine\ORM\EntityManagerInterface;

class CdnSynchronizer
{
    /**
     * @var VideoService $videoService
     */
    private VideoService $videoService;

    /**
     * @var FolderService $folderService
     */
    private FolderService $folderService;

    /**
     * @var JwtService $jwtService
     */
    private JwtService $jwtService;

    /**
     * @var AccountService $accountService
     */
    private AccountService $accountService;

    /**
     * @var EntityManagerInterface $em
     */
    private EntityManagerInterface $em;

    /**
     * @var CdnManager $cdnManager
     */
    private CdnManager $cdnManager;

    /**
     * @param VideoService $videoService
     * @param FolderService $folderService
     * @param JwtService $jwtService
     * @param AccountService $accountService
     * @param EntityManagerInterface $em
     * @param CdnManager $cdnManager
     */
    public function __construct(
        VideoService $videoService,
        FolderService $folderService,
        JwtService $jwtService,
        AccountService $accountService,
        EntityManagerInterface $em,
        CdnManager $cdnManager
    )
    {
        $this->videoService = $videoService;
        $this->folderService = $folderService;
        $this->jwtService = $jwtService;
        $this->accountService = $accountService;
        $this->em = $em;
        $this->cdnManager = $cdnManager;
    }


    /**
     * @param CdnVideoRequest $videoData
     * @return Video
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function synchronize(CdnVideoRequest $videoData): Video
    {
        try {
            $video = $this->findOrCreateVideo($videoData);
            $this->updateVideoDetails($video, $videoData);
            return $video;
        } catch (UnauthorizedException) {
            throw new BadRequestException("Cannot decode video token");
        }
    }

    /**
     * @param CdnVideoRequest $videoData
     * @return Video
     * @throws BadRequestException
     * @throws InternalException
     * @throws NotFoundException
     */
    public function synchronizeThumbnail(CdnVideoRequest $videoData): Video
    {
        try {
            $video = $this->findOrCreateVideo($videoData);
            $thumbnailContent = $this->cdnManager->getThumbnailContent($video, 1);
            $video->setThumbnail(base64_encode($thumbnailContent));
            return $video;
        } catch (UnauthorizedException) {
            throw new BadRequestException("Cannot decode video token");
        }
    }

    /**
     * @param CdnVideoRequest $videoData
     * @return Video
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws UnauthorizedException
     */
    private function findOrCreateVideo(CdnVideoRequest $videoData): Video
    {
        $cdnId = $videoData->id;
        $video = $this->videoService->getVideoByCdnId($cdnId);

        if (!$video) {
            $token = $this->getVideoToken($videoData);
            $decodedToken = $this->jwtService->decodeToken($token, JwtUsage::USAGE_UPLOAD);
            $account = $this->accountService->getAccountById($decodedToken['user_id']);

            $video = new Video($decodedToken['name'], $account);
            $video->setCdnId($cdnId);
            $this->updateFolder($video, $decodedToken['folder']);
            $this->em->persist($video);
        }

        return $video;
    }

    /**
     * @param CdnVideoRequest $videoData
     * @return string
     * @throws BadRequestException
     */
    private function getVideoToken(CdnVideoRequest $videoData): string
    {
        if (!isset($videoData->parameters['video_token'])) {
            throw new BadRequestException("Video token is missing.");
        }

        return $videoData->parameters['video_token'];
    }

    /**
     * @param Video $video
     * @param CdnVideoRequest $videoData
     * @return void
     */
    private function updateVideoDetails(Video $video, CdnVideoRequest $videoData): void
    {
        $video->setCodec($videoData->codec);
        $video->setExtension($videoData->extension);
        $video->setLength($videoData->length);
        $video->setPath($videoData->path);
        $this->updateVideoResolution($video, $videoData);
        $this->updateVideoMd5($video, $videoData);

        if (!$video->getSize() && $storage = $video->getAccount()->getStorage()) {
            $video->setSize($videoData->size);
            $storage->setUsedStorage($storage->getUsedStorage() + $videoData->size);
        }
    }

    /**
     * @param Video $video
     * @param int|null $folderId
     * @return void
     */
    private function updateFolder(Video $video, ?int $folderId): void
    {
        if ($folderId) {
            try {
                $video->setFolder($this->folderService->getFolderById($folderId));
            } catch (NotFoundException) {
                $video->setFolder(null);
            }
        }
    }

    /**
     * @param Video $video
     * @param CdnVideoRequest $videoData
     * @return void
     */
    private function updateVideoResolution(Video $video, CdnVideoRequest $videoData): void
    {
        $video->setOriginalWidth($videoData->resolution->width);
        $video->setOriginalHeight($videoData->resolution->height);
    }

    /**
     * @param Video $video
     * @param CdnVideoRequest $videoData
     * @return void
     */
    private function updateVideoMd5(Video $video, CdnVideoRequest $videoData): void
    {
        if ($videoData->md5) {
            $md5 = $this->videoService->getMd5ByHash($videoData->md5);
            if (!$md5) {
                $md5 = new MD5($videoData->md5);
                $this->em->persist($md5);
            }
            $video->setMd5($md5);
        }
    }

}
