<?php

namespace App\Service\Cdn;

use App\DTO\Video\CdnVideoRequest;
use App\DTO\Video\UploadResponse;
use App\Entity\Video\Video;
use App\Exception\BadRequestException;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Helper\Cdn\CdnHasher;
use App\Helper\Cdn\CdnManager;
use App\Helper\Cdn\CdnSynchronizer;
use App\Helper\Generator\RandomGenerator;
use App\Service\Video\VideoService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class CdnService
{
    /**
     * @var string $cdnProjectId
     */
    private string $cdnProjectId;

    /**
     * @var string $cdnSecretKey
     */
    private string $cdnSecretKey;

    /**
     * @var string $cdnCallbackKey
     */
    private string $cdnCallbackKey;

    /**
     * @var LoggerInterface $logger
     */
    private LoggerInterface $logger;

    /**
     * @var CdnHasher $cdnHasher
     */
    private CdnHasher $cdnHasher;

    /**
     * @var CdnSynchronizer $cdnSynchronizer
     */
    private CdnSynchronizer $cdnSynchronizer;

    /**
     * @var CdnManager $cdnManager
     */
    private CdnManager $cdnManager;

    /**
     * @var EntityManagerInterface $em
     */
    private EntityManagerInterface $em;

    /**
     * @var RandomGenerator $generator
     */
    private RandomGenerator $generator;

    /**
     * @var VideoService $videoService
     */
    private VideoService $videoService;

    /**
     * @param string $cdnProjectId
     * @param string $cdnSecretKey
     * @param string $cdnCallbackKey
     * @param LoggerInterface $logger
     * @param CdnHasher $cdnHasher
     * @param CdnSynchronizer $cdnSynchronizer
     * @param CdnManager $cdnManager
     * @param EntityManagerInterface $em
     * @param RandomGenerator $generator
     * @param VideoService $videoService
     */
    public function __construct(
        string $cdnProjectId,
        string $cdnSecretKey,
        string $cdnCallbackKey,
        LoggerInterface $logger,
        CdnHasher $cdnHasher,
        CdnSynchronizer $cdnSynchronizer,
        CdnManager $cdnManager,
        EntityManagerInterface $em,
        RandomGenerator $generator,
        VideoService $videoService
    )
    {
        $this->cdnProjectId = $cdnProjectId;
        $this->cdnSecretKey = $cdnSecretKey;
        $this->cdnCallbackKey = $cdnCallbackKey;
        $this->logger = $logger;
        $this->cdnHasher = $cdnHasher;
        $this->cdnSynchronizer = $cdnSynchronizer;
        $this->cdnManager = $cdnManager;
        $this->em = $em;
        $this->generator = $generator;
        $this->videoService = $videoService;
    }

    /**
     * @return string
     */
    public function getCdnCallbackKey(): string
    {
        return $this->cdnCallbackKey;
    }

    /**
     * @param array $data
     * @return UploadResponse
     * @throws InternalException
     */
    public function createUploadData(array $data): UploadResponse
    {
        $data['projectId'] = $this->cdnProjectId;
        $data['nonce'] = $this->generator->generateString(32);
        $this->cdnHasher->addSignature($data, $this->cdnSecretKey);
        $this->logger->info('Generated upload data.', $data);

        return new UploadResponse(
            $data['nonce'],
            $data['params'],
            $data['projectId'],
            $data['signature']
        );
    }

    /**
     * @param Video $video
     * @param int $quality
     * @return string
     * @throws InternalException
     */
    public function getManifest(Video $video, int $quality): string
    {
        return $this->cdnManager->getManifestContent($video, $quality);
    }

    /**
     * @param Video $video
     * @return string
     * @throws InternalException
     */
    public function getDownloadLink(Video $video): string
    {
        return $this->cdnManager->getDownloadContent($video)['downloadLink'];
    }

    /**
     * @param CdnVideoRequest $videoData
     * @return void
     * @throws BadRequestException
     * @throws NotFoundException
     */
    public function synchronizeVideo(CdnVideoRequest $videoData): void
    {
        try {
            if ($videoData->deleted) {
                $video = $this->videoService->getVideoByCdnId($videoData->id);
                $this->videoService->deleteVideos([$video]);
                $this->logger->info("Deleted video.", [
                    "cdnId" => $videoData->id
                ]);
                return;
            }

            $this->cdnSynchronizer->synchronize($videoData);
            $this->logger->info("Synchronized video.", [
                "cdnId" => $videoData->id
            ]);
            $this->em->flush();
        } catch (BadRequestException|NotFoundException $e) {
            $this->logger->error("Error synchronizing video.", [
                "message" => $e->getMessage(),
                "cdnId" => $videoData->id
            ]);
            throw $e;
        }
    }

    /**
     * @param CdnVideoRequest $videoData
     * @return void
     * @throws BadRequestException
     * @throws InternalException
     * @throws NotFoundException
     */
    public function synchronizeThumbnail(CdnVideoRequest $videoData): void
    {
        try {
            $this->cdnSynchronizer->synchronizeThumbnail($videoData);
            $this->logger->info("Synchronized thumbnail.", [
                "cdnId" => $videoData->id
            ]);
            $this->em->flush();
        } catch (BadRequestException|NotFoundException $e) {
            $this->logger->error("Error synchronizing thumb.", [
                "message" => $e->getMessage(),
                "cdnId" => $videoData->id
            ]);
            throw $e;
        }
    }
}
