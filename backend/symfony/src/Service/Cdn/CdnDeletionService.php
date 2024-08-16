<?php

namespace App\Service\Cdn;

use App\Exception\InternalException;
use App\Helper\Cdn\CdnManager;
use Psr\Log\LoggerInterface;

class CdnDeletionService
{
    /**
     * @var CdnManager $cdnManager
     */
    private CdnManager $cdnManager;

    /**
     * @var LoggerInterface $logger
     */
    private LoggerInterface $logger;

    /**
     * @param CdnManager $cdnManager
     * @param LoggerInterface $logger
     */
    public function __construct(CdnManager $cdnManager, LoggerInterface $logger)
    {
        $this->cdnManager = $cdnManager;
        $this->logger = $logger;
    }

    /**
     * @param array $videos
     * @return void
     */
    public function batchDelete(array $videos): void
    {
        try {
            $this->cdnManager->batchDelete($videos);
        } catch (InternalException $e) {
            $this->logger->error("Error deleting videos.", [
                "message" => $e->getMessage()
            ]);
        }
    }
}
