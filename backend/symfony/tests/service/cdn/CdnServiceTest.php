<?php

namespace App\Tests\Service\Cdn;

use App\DTO\Video\CdnVideoRequest;
use App\Entity\Video\Video;
use App\Exception\BadRequestException;
use App\Exception\InternalException;
use App\Exception\NotFoundException;
use App\Helper\Cdn\CdnHasher;
use App\Helper\Cdn\CdnManager;
use App\Helper\Cdn\CdnSynchronizer;
use App\Helper\Generator\RandomGenerator;
use App\Service\Cdn\CdnService;
use App\Service\Video\VideoService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class CdnServiceTest extends TestCase
{
    private $cdnService;
    private $mockLogger;
    private $mockCdnHasher;
    private $mockCdnSynchronizer;
    private $mockCdnManager;
    private $mockEntityManager;
    private $mockRandomGenerator;
    private $mockVideoService;

    protected function setUp(): void
    {
        $this->mockLogger = $this->createMock(LoggerInterface::class);
        $this->mockCdnHasher = $this->createMock(CdnHasher::class);
        $this->mockCdnSynchronizer = $this->createMock(CdnSynchronizer::class);
        $this->mockCdnManager = $this->createMock(CdnManager::class);
        $this->mockEntityManager = $this->createMock(EntityManagerInterface::class);
        $this->mockRandomGenerator = $this->createMock(RandomGenerator::class);
        $this->mockVideoService = $this->createMock(VideoService::class);

        $this->cdnService = new CdnService(
            'project_id',
            'secret_key',
            'callback_key',
            $this->mockLogger,
            $this->mockCdnHasher,
            $this->mockCdnSynchronizer,
            $this->mockCdnManager,
            $this->mockEntityManager,
            $this->mockRandomGenerator,
            $this->mockVideoService
        );
    }

    public function testGetCdnCallbackKey()
    {
        $result = $this->cdnService->getCdnCallbackKey();
        $this->assertEquals('callback_key', $result);
    }

    public function testCreateUploadDataSuccess()
    {
        $data = ['param1' => 'value1', 'size' => '123'];
        $this->mockCdnHasher->expects($this->once())
            ->method('addSignature')
            ->willReturnCallback(function(&$data, $secretKey) {
                $data['signature'] = 'dummy_signature';
            });

        $result = $this->cdnService->createUploadData($data);

        $this->assertArrayHasKey('project_id', $result);
        $this->assertArrayHasKey('nonce', $result);
        $this->assertArrayHasKey('signature', $result);
    }

    public function testGetManifestSuccess()
    {
        $video = $this->createMock(Video::class);
        $this->mockCdnManager->expects($this->once())
            ->method('getManifestContent')
            ->with($video)
            ->willReturn('manifest content');

        $result = $this->cdnService->getManifest($video);

        $this->assertEquals('manifest content', $result);
    }

    public function testGetManifestInternalError()
    {
        $this->expectException(InternalException::class);

        $video = $this->createMock(Video::class);
        $this->mockCdnManager->expects($this->once())
            ->method('getManifestContent')
            ->with($video)
            ->willThrowException(new InternalException('Error retrieving manifest'));

        $this->cdnService->getManifest($video);
    }

    public function testSynchronizeVideoSuccess()
    {
        $videoData = new CdnVideoRequest();
        $videoData->id = 'video_id';
        $videoData->deleted = false;

        $this->mockCdnSynchronizer->expects($this->once())
            ->method('synchronize')
            ->with($videoData);

        $this->mockEntityManager->expects($this->once())
            ->method('flush');

        $this->cdnService->synchronizeVideo($videoData);

        $this->assertTrue(true);
    }

    public function testSynchronizeVideoBadRequest()
    {
        $this->expectException(BadRequestException::class);

        $videoData = new CdnVideoRequest();
        $videoData->id = 'video_id';
        $videoData->deleted = false;

        $this->mockCdnSynchronizer->expects($this->once())
            ->method('synchronize')
            ->with($videoData)
            ->willThrowException(new BadRequestException('Bad request'));

        $this->cdnService->synchronizeVideo($videoData);
    }

    public function testSynchronizeVideoNotFound()
    {
        $this->expectException(NotFoundException::class);

        $videoData = new CdnVideoRequest();
        $videoData->id = 'video_id';
        $videoData->deleted = false;

        $this->mockCdnSynchronizer->expects($this->once())
            ->method('synchronize')
            ->with($videoData)
            ->willThrowException(new NotFoundException('Video not found'));

        $this->cdnService->synchronizeVideo($videoData);
    }
}
