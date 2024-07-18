<?php

namespace App\Service\Cdn;

use App\DTO\Video\CdnVideoRequest;
use App\Entity\Video\Video;
use App\Exception\BadRequestException;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Helper\Cdn\CdnHasher;
use App\Helper\Cdn\CdnManager;
use App\Helper\Cdn\CdnSynchronizer;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
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
     * @param string $cdnProjectId
     * @param string $cdnSecretKey
     * @param string $cdnCallbackKey
     * @param LoggerInterface $logger
     * @param CdnHasher $cdnHasher
     * @param CdnSynchronizer $cdnSynchronizer
     * @param CdnManager $cdnManager
     * @param EntityManagerInterface $em
     */
    public function __construct(
        string $cdnProjectId,
        string $cdnSecretKey,
        string $cdnCallbackKey,
        LoggerInterface $logger,
        CdnHasher $cdnHasher,
        CdnSynchronizer $cdnSynchronizer,
        CdnManager $cdnManager,
        EntityManagerInterface $em
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
     * @return array
     * @throws InternalException
     */
    public function createUploadData(array $data): array
    {
        try {
            $data['project_id'] = $this->cdnProjectId;
            $data['nonce'] = bin2hex(random_bytes(16));
            $this->cdnHasher->addSignature($data, $this->cdnSecretKey);
            $this->logger->info('Generated upload data.', $data);
            return $data;
        } catch (Exception) {
            throw new InternalException("Failed to generate nonce.");
        }
    }

    /**
     * @param Video $video
     * @return string
     * @throws InternalException
     */
    public function getManifest(Video $video): string
    {
        return $this->cdnManager->getManifestContent($video);
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
     * @param array $thumbs
     * @return void
     * @throws BadRequestException
     * @throws NotFoundException
     * @throws InternalException
     */
    public function synchronizeThumbnail(CdnVideoRequest $videoData, array $thumbs): void
    {
        try {
            $this->cdnSynchronizer->synchronizeThumbnail($videoData, $thumbs);
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
