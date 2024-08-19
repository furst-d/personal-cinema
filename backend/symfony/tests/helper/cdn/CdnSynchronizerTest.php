<?php

namespace App\Tests\Helper\Cdn;

use App\DTO\Video\CdnVideoRequest;
use App\DTO\Video\CdnVideoResolutionRequest;
use App\Entity\Account\Account;
use App\Entity\Storage\Storage;
use App\Entity\Video\Video;
use App\Helper\Cdn\CdnManager;
use App\Helper\Cdn\CdnSynchronizer;
use App\Service\Account\AccountService;
use App\Service\Jwt\JwtService;
use App\Service\Video\FolderService;
use App\Service\Video\VideoService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class CdnSynchronizerTest extends TestCase
{
    private $cdnSynchronizer;
    private $mockVideoService;
    private $mockFolderService;
    private $mockJwtService;
    private $mockAccountService;
    private $mockEntityManager;
    private $cdnManager;

    protected function setUp(): void
    {
        $this->mockVideoService = $this->createMock(VideoService::class);
        $this->mockFolderService = $this->createMock(FolderService::class);
        $this->mockJwtService = $this->createMock(JwtService::class);
        $this->mockAccountService = $this->createMock(AccountService::class);
        $this->mockEntityManager = $this->createMock(EntityManagerInterface::class);
        $this->cdnManager = $this->createMock(CdnManager::class);

        $this->cdnSynchronizer = new CdnSynchronizer(
            $this->mockVideoService,
            $this->mockFolderService,
            $this->mockJwtService,
            $this->mockAccountService,
            $this->mockEntityManager,
            $this->cdnManager
        );
    }

    private function createValidCdnVideoRequest(): CdnVideoRequest
    {
        $videoData = new CdnVideoRequest();
        $videoData->id = 'valid-uuid';
        $videoData->deleted = false;
        $videoData->title = 'Test Video';
        $videoData->status = 'uploaded';
        $videoData->codec = 'H264';
        $videoData->extension = 'mp4';
        $videoData->size = 123456;
        $videoData->length = 600;
        $videoData->path = '/videos/test.mp4';
        $videoData->resolution = new CdnVideoResolutionRequest();
        $videoData->resolution->width = 1920;
        $videoData->resolution->height = 1080;
        $videoData->parameters = ['video_token' => 'valid-token'];
        $videoData->md5 = 'valid-md5-hash';
        $videoData->createdAt = new \DateTimeImmutable();
        $videoData->updatedAt = new \DateTimeImmutable();
        $videoData->conversions = [];

        return $videoData;
    }

    public function testSynchronizeSuccess()
    {
        $videoData = $this->createValidCdnVideoRequest();
        $video = $this->createMock(Video::class);
        $video->storage = new Storage(new Account('email@test.cz', 'password', 'salt', 10), 10);

        $this->mockVideoService->method('getVideoByCdnId')->willReturn($video);
        $this->mockJwtService->method('decodeToken')->willReturn(['user_id' => 1]);
        $this->mockAccountService->method('getAccountById')->willReturn($this->createMock(Account::class));

        $result = $this->cdnSynchronizer->synchronize($videoData);

        $this->assertInstanceOf(Video::class, $result);
    }
}