<?php

namespace App\Tests\Service\Video;

use App\DTO\PaginatorRequest;
use App\Entity\Account\Account;
use App\Entity\Video\Folder;
use App\Entity\Video\MD5;
use App\Entity\Video\Video;
use App\Exception\NotFoundException;
use App\Helper\Generator\UrlGenerator;
use App\Helper\DTO\PaginatorResult;
use App\Helper\Video\FolderData;
use App\Repository\Video\ConversionRepository;
use App\Repository\Video\MD5Repository;
use App\Repository\Video\VideoRepository;
use App\Service\Cdn\CdnDeletionService;
use App\Service\Video\FolderService;
use App\Service\Video\ShareService;
use App\Service\Video\VideoService;
use PHPUnit\Framework\TestCase;

class VideoServiceTest extends TestCase
{
    private $videoService;
    private $mockVideoRepository;
    private $mockMd5Repository;
    private $mockUrlGenerator;
    private $mockShareService;
    private $mockFolderService;
    private $mockCdnDeletionService;

    protected function setUp(): void
    {
        $this->mockVideoRepository = $this->createMock(VideoRepository::class);
        $this->mockMd5Repository = $this->createMock(MD5Repository::class);
        $this->mockUrlGenerator = $this->createMock(UrlGenerator::class);
        $this->mockShareService = $this->createMock(ShareService::class);
        $this->mockFolderService = $this->createMock(FolderService::class);
        $this->mockCdnDeletionService = $this->createMock(CdnDeletionService::class);

        $this->videoService = new VideoService(
            $this->mockVideoRepository,
            $this->mockMd5Repository,
            $this->mockUrlGenerator,
            $this->mockShareService,
            $this->mockFolderService,
            $this->mockCdnDeletionService
        );
    }

    public function testGetVideoByCdnId()
    {
        $account = new Account('email@example.com', 'password', 'salt', 10);
        $video = new Video('testVideo', $account);
        $this->mockVideoRepository->method('findOneBy')->willReturn($video);

        $result = $this->videoService->getVideoByCdnId('cdnId');

        $this->assertSame($video, $result);
    }

    public function testGetVideoByCdnIdNotFound()
    {
        $this->expectException(NotFoundException::class);

        $this->mockVideoRepository->method('findOneBy')->willReturn(null);

        $this->videoService->getVideoByCdnId('cdnId');
    }

    public function testGetAccountVideoById()
    {
        $account = new Account('email@example.com', 'password', 'salt', 10);
        $video = new Video('testVideo', $account);
        $this->mockVideoRepository->method('findOneBy')->willReturn($video);

        $result = $this->videoService->getAccountVideoById($account, 1);

        $this->assertSame($video, $result);
    }

    public function testGetAccountVideoByIdNotFound()
    {
        $this->expectException(NotFoundException::class);

        $account = new Account('email@example.com', 'password', 'salt', 10);
        $this->mockVideoRepository->method('findOneBy')->willReturn(null);

        $this->videoService->getAccountVideoById($account, 1);
    }

    public function testGetVideoById()
    {
        $account = new Account('email@example.com', 'password', 'salt', 10);
        $video = new Video('testVideo', $account);
        $this->mockVideoRepository->method('find')->willReturn($video);

        $result = $this->videoService->getVideoById(1);

        $this->assertSame($video, $result);
    }

    public function testGetVideoByIdNotFound()
    {
        $this->expectException(NotFoundException::class);

        $this->mockVideoRepository->method('find')->willReturn(null);

        $this->videoService->getVideoById(1);
    }

    public function testGetMd5ByHash()
    {
        $md5 = new MD5('hash');
        $this->mockMd5Repository->method('findOneBy')->willReturn($md5);

        $result = $this->videoService->getMd5ByHash('hash');

        $this->assertSame($md5, $result);
    }

    public function testGetMd5ByHashNotFound()
    {
        $this->mockMd5Repository->method('findOneBy')->willReturn(null);

        $result = $this->videoService->getMd5ByHash('hash');

        $this->assertNull($result);
    }

    public function testGetVideos()
    {
        $account = new Account('email@example.com', 'password', 'salt', 10);
        $folder = new Folder('Test Folder', $account);
        $folderData = new FolderData($folder, false);
        $videos = [new Video('Video 1', $account), new Video('Video 2', $account)];
        $paginatorRequest =  new PaginatorRequest(10, 0);
        $paginatorResult = new PaginatorResult($videos, 2);

        $this->mockVideoRepository
            ->expects($this->once())
            ->method('findVideos')
            ->with($account, $folderData, $paginatorRequest)
            ->willReturn($paginatorResult);

        $result = $this->videoService->getVideos($account, $folderData, $paginatorRequest);

        $this->assertSame($paginatorResult, $result);
    }

    public function testUpdateVideo()
    {
        $account = new Account('email@example.com', 'password', 'salt', 10);
        $video = new Video('Old Video', $account);
        $folder = new Folder('Test Folder', $account);

        $this->mockVideoRepository
            ->expects($this->once())
            ->method('save')
            ->with($video);

        $this->videoService->updateVideo($video, 'Updated Video', $folder);

        $this->assertSame('Updated Video', $video->getName());
        $this->assertSame($folder, $video->getFolder());
    }

    public function testDeleteVideo()
    {
        $account = new Account('email@example.com', 'password', 'salt', 10);
        $video = new Video('testVideo', $account);

        $this->mockVideoRepository
            ->expects($this->once())
            ->method('delete')
            ->with($video);

        $this->videoService->deleteVideos([$video]);
    }
}
