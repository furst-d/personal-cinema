<?php

namespace App\Tests\Service\Video;

use App\Entity\Account\Account;
use App\Entity\Video\MD5;
use App\Entity\Video\Video;
use App\Exception\NotFoundException;
use App\Repository\Video\MD5Repository;
use App\Repository\Video\VideoRepository;
use App\Service\Video\VideoService;
use PHPUnit\Framework\TestCase;

class VideoServiceTest extends TestCase
{
    private $videoService;
    private $mockVideoRepository;
    private $mockMd5Repository;

    protected function setUp(): void
    {
        $this->mockVideoRepository = $this->createMock(VideoRepository::class);
        $this->mockMd5Repository = $this->createMock(MD5Repository::class);

        $this->videoService = new VideoService(
            $this->mockVideoRepository,
            $this->mockMd5Repository
        );
    }

    public function testGetVideoByCdnId()
    {
        $account = new Account('email@example.com', 'password', 'salt');
        $video = new Video('testVideo', $account);
        $this->mockVideoRepository->method('findOneBy')->willReturn($video);

        $result = $this->videoService->getVideoByCdnId('cdnId');

        $this->assertSame($video, $result);
    }

    public function testGetVideoByCdnIdNotFound()
    {
        $this->mockVideoRepository->method('findOneBy')->willReturn(null);

        $result = $this->videoService->getVideoByCdnId('cdnId');

        $this->assertNull($result);
    }

    public function testGetAccountVideoById()
    {
        $account = new Account('email@example.com', 'password', 'salt');
        $video = new Video('testVideo', $account);
        $this->mockVideoRepository->method('findOneBy')->willReturn($video);

        $result = $this->videoService->getAccountVideoById($account, 1);

        $this->assertSame($video, $result);
    }

    public function testGetAccountVideoByIdNotFound()
    {
        $this->expectException(NotFoundException::class);

        $account = new Account('email@example.com', 'password', 'salt');
        $this->mockVideoRepository->method('findOneBy')->willReturn(null);

        $this->videoService->getAccountVideoById($account, 1);
    }

    public function testGetVideoById()
    {
        $account = new Account('email@example.com', 'password', 'salt');
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
}
