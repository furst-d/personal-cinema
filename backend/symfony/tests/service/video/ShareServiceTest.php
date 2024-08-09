<?php

namespace App\Tests\Service\Video;

use App\Entity\Account\Account;
use App\Entity\Video\Folder;
use App\Entity\Video\Share\ShareVideo;
use App\Entity\Video\Share\ShareVideoPublic;
use App\Entity\Video\Share\ShareVideoPublicView;
use App\Entity\Video\Video;
use App\Exception\ConflictException;
use App\Exception\ForbiddenException;
use App\Exception\NotFoundException;
use App\Helper\Generator\RandomGenerator;
use App\Repository\Settings\SettingsRepository;
use App\Repository\Video\Share\ShareFolderRepository;
use App\Repository\Video\Share\ShareVideoPublicRepository;
use App\Repository\Video\Share\ShareVideoPublicViewRepository;
use App\Repository\Video\Share\ShareVideoRepository;
use App\Service\Video\ShareService;
use PHPUnit\Framework\TestCase;

class ShareServiceTest extends TestCase
{
    private ShareService $shareService;
    private $mockShareFolderRepository;
    private $mockShareVideoPublicRepository;
    private $mockShareVideoPublicViewRepository;
    private $mockShareVideoRepository;
    private $mockSettingsRepository;
    private $mockRandomGenerator;

    protected function setUp(): void
    {
        $this->mockShareFolderRepository = $this->createMock(ShareFolderRepository::class);
        $this->mockShareVideoPublicRepository = $this->createMock(ShareVideoPublicRepository::class);
        $this->mockShareVideoPublicViewRepository = $this->createMock(ShareVideoPublicViewRepository::class);
        $this->mockShareVideoRepository = $this->createMock(ShareVideoRepository::class);
        $this->mockSettingsRepository = $this->createMock(SettingsRepository::class);
        $this->mockRandomGenerator = $this->createMock(RandomGenerator::class);

        $this->shareService = new ShareService(
            $this->mockShareFolderRepository,
            $this->mockShareVideoPublicRepository,
            $this->mockShareVideoPublicViewRepository,
            $this->mockShareVideoRepository,
            $this->mockSettingsRepository,
            $this->mockRandomGenerator
        );
    }

    public function testAddViewSuccess()
    {
        $video = $this->createMock(Video::class);
        $sessionId = 'session_id';
        $publicShare = $this->createMock(ShareVideoPublic::class);

        $this->mockShareVideoPublicRepository->expects($this->once())
            ->method('findValidByVideo')
            ->with($video)
            ->willReturn($publicShare);

        $this->mockShareVideoPublicViewRepository->expects($this->once())
            ->method('findShareViews')
            ->with($publicShare, $sessionId)
            ->willReturn([]);

        $this->mockSettingsRepository->expects($this->once())
            ->method('getPublicLinkViewLimit')
            ->willReturn('10');

        $this->mockShareVideoPublicViewRepository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(ShareVideoPublicView::class));

        $this->shareService->addView($video, $sessionId);

        $this->assertTrue(true);
    }

    public function testAddViewThrowsNotFoundException()
    {
        $this->expectException(NotFoundException::class);

        $video = $this->createMock(Video::class);
        $sessionId = 'session_id';

        $this->mockShareVideoPublicRepository->expects($this->once())
            ->method('findValidByVideo')
            ->with($video)
            ->willReturn(null);

        $this->shareService->addView($video, $sessionId);
    }

    public function testAddViewThrowsForbiddenException()
    {
        $this->expectException(ForbiddenException::class);

        $video = $this->createMock(Video::class);
        $sessionId = 'session_id';
        $publicShare = $this->createMock(ShareVideoPublic::class);

        $this->mockShareVideoPublicRepository->expects($this->once())
            ->method('findValidByVideo')
            ->with($video)
            ->willReturn($publicShare);

        $this->mockShareVideoPublicViewRepository->expects($this->once())
            ->method('findShareViews')
            ->with($publicShare, $sessionId)
            ->willReturn([]);

        $this->mockSettingsRepository->expects($this->once())
            ->method('getPublicLinkViewLimit')
            ->willReturn('0'); // Exceeded limit simulation

        $this->shareService->addView($video, $sessionId);
    }
}
