<?php

namespace App\Service\Cdn;

use App\Exception\InternalException;
use App\Helper\Cdn\CdnHasher;
use Exception;
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
     * @var LoggerInterface $logger
     */
    private LoggerInterface $logger;

    /**
     * @var CdnHasher $cdnHasher
     */
    private CdnHasher $cdnHasher;

    /**
     * @param string $cdnProjectId
     * @param string $cdnSecretKey
     * @param LoggerInterface $logger
     * @param CdnHasher $cdnHasher
     */
    public function __construct(
        string $cdnProjectId,
        string $cdnSecretKey,
        LoggerInterface $logger,
        CdnHasher $cdnHasher
    )
    {
        $this->cdnProjectId = $cdnProjectId;
        $this->cdnSecretKey = $cdnSecretKey;
        $this->logger = $logger;
        $this->cdnHasher = $cdnHasher;
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
}
