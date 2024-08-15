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
use App\Repository\Video\FolderRepository;
use App\Repository\Video\Share\ShareFolderRepository;
use App\Repository\Video\Share\ShareVideoPublicRepository;
use App\Repository\Video\Share\ShareVideoPublicViewRepository;
use App\Repository\Video\Share\ShareVideoRepository;
use App\Repository\Video\VideoRepository;
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
    private $mockVideoRepository;
    private $mockFolderRepository;
    private $mockRandomGenerator;

    protected function setUp(): void
    {
        $this->mockShareFolderRepository = $this->createMock(ShareFolderRepository::class);
        $this->mockShareVideoPublicRepository = $this->createMock(ShareVideoPublicRepository::class);
        $this->mockShareVideoPublicViewRepository = $this->createMock(ShareVideoPublicViewRepository::class);
        $this->mockShareVideoRepository = $this->createMock(ShareVideoRepository::class);
        $this->mockSettingsRepository = $this->createMock(SettingsRepository::class);
        $this->mockVideoRepository = $this->createMock(VideoRepository::class);
        $this->mockFolderRepository = $this->createMock(FolderRepository::class);
        $this->mockRandomGenerator = $this->createMock(RandomGenerator::class);

        $this->shareService = new ShareService(
            $this->mockShareFolderRepository,
            $this->mockShareVideoPublicRepository,
            $this->mockShareVideoPublicViewRepository,
            $this->mockShareVideoRepository,
            $this->mockSettingsRepository,
            $this->mockVideoRepository,
            $this->mockFolderRepository,
            $this->mockRandomGenerator
        );
    }

    public function testHasSharedVideoAccess()
    {
        $account = $this->createMock(Account::class);
        $video = $this->createMock(Video::class);

        $this->mockShareVideoRepository->expects($this->once())
            ->method('hasSharedVideoAccess')
            ->with($account, $video)
            ->willReturn(true);

        $result = $this->shareService->hasSharedVideoAccess($account, $video);

        $this->assertTrue($result);
    }

    public function testHasSharedFolderAccess()
    {
        $account = $this->createMock(Account::class);
        $folder = $this->createMock(Folder::class);

        $this->mockShareFolderRepository->expects($this->once())
            ->method('hasSharedFolderAccess')
            ->with($account, $folder)
            ->willReturn(true);

        $result = $this->shareService->hasSharedFolderAccess($account, $folder);

        $this->assertTrue($result);
    }

    public function testCreateVideoShareSuccess()
    {
        $account = $this->createMock(Account::class);
        $video = $this->createMock(Video::class);

        $this->mockShareVideoRepository->expects($this->once())
            ->method('isVideoAlreadyShared')
            ->with($account, $video)
            ->willReturn(false);

        $this->mockShareVideoRepository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(ShareVideo::class));

        $result = $this->shareService->createVideoShare($account, $video);

        $this->assertInstanceOf(ShareVideo::class, $result);
    }

    public function testCreateVideoShareThrowsConflictException()
    {
        $this->expectException(ConflictException::class);

        $account = $this->createMock(Account::class);
        $video = $this->createMock(Video::class);

        $this->mockShareVideoRepository->expects($this->once())
            ->method('isVideoAlreadyShared')
            ->with($account, $video)
            ->willReturn(true);

        $this->shareService->createVideoShare($account, $video);
    }

    public function testCreatePublicVideoShareLinkSuccess()
    {
        $video = $this->createMock(Video::class);

        $this->mockShareVideoPublicRepository->expects($this->once())
            ->method('findValidByVideo')
            ->with($video)
            ->willReturn(null);

        $this->mockRandomGenerator->expects($this->once())
            ->method('generateString')
            ->with(64)
            ->willReturn('random_hash');

        $this->mockShareVideoPublicRepository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(ShareVideoPublic::class));

        $result = $this->shareService->createPublicVideoShareLink($video);

        $this->assertInstanceOf(ShareVideoPublic::class, $result);
        $this->assertEquals('random_hash', $result->getHash());
    }

    public function testCreatePublicVideoShareLinkThrowsForbiddenException()
    {
        $this->expectException(ForbiddenException::class);

        $video = $this->createMock(Video::class);
        $publicShare = $this->createMock(ShareVideoPublic::class);

        $this->mockShareVideoPublicRepository->expects($this->once())
            ->method('findValidByVideo')
            ->with($video)
            ->willReturn($publicShare);

        $this->shareService->createPublicVideoShareLink($video);
    }

    public function testGetPublicVideoByHashSuccess()
    {
        $hash = 'valid_hash';
        $sessionId = 'session_id';

        $publicShare = $this->createMock(ShareVideoPublic::class);

        $this->mockShareVideoPublicRepository->expects($this->once())
            ->method('findValidByHash')
            ->with($hash)
            ->willReturn($publicShare);

        $this->mockShareVideoPublicViewRepository->expects($this->once())
            ->method('findShareViews')
            ->with($publicShare, $sessionId)
            ->willReturn([]);

        $this->mockSettingsRepository->expects($this->once())
            ->method('getPublicLinkViewLimit')
            ->willReturn('10');

        $result = $this->shareService->getPublicVideoByHash($hash, $sessionId);

        $this->assertInstanceOf(ShareVideoPublic::class, $result);
    }

    public function testGetPublicVideoByHashThrowsNotFoundException()
    {
        $this->expectException(NotFoundException::class);

        $hash = 'invalid_hash';
        $sessionId = 'session_id';

        $this->mockShareVideoPublicRepository->expects($this->once())
            ->method('findValidByHash')
            ->with($hash)
            ->willReturn(null);

        $this->shareService->getPublicVideoByHash($hash, $sessionId);
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
