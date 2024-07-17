<?php

namespace App\Tests\Helper\Cdn;

use App\Entity\Video\Video;
use App\Exception\InternalException;
use App\Helper\Cdn\CdnManager;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class CdnManagerTest extends TestCase
{
    private $cdnManager;
    private $mockClient;

    protected function setUp(): void
    {
        $this->mockClient = $this->createMock(Client::class);
        $this->cdnManager = new CdnManager('secretKey', 'http://cdn.example.com');
        $this->cdnManager->setClient($this->mockClient);
    }

    public function testGetManifestContentSuccess()
    {
        $video = $this->createMock(Video::class);
        $video->method('getCdnId')->willReturn('videoCdnId');

        $mockStream = $this->createMock(StreamInterface::class);
        $mockStream->method('getContents')->willReturn('manifest content');

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('getStatusCode')->willReturn(200);
        $mockResponse->method('getBody')->willReturn($mockStream);

        $this->mockClient->method('get')->willReturn($mockResponse);

        $result = $this->cdnManager->getManifestContent($video);

        $this->assertEquals('manifest content', $result);
    }

    public function testGetManifestContentErrorResponse()
    {
        $this->expectException(InternalException::class);
        $this->expectExceptionMessage('Error retrieving manifest from CDN');

        $video = $this->createMock(Video::class);
        $video->method('getCdnId')->willReturn('videoCdnId');

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('getStatusCode')->willReturn(500);

        $this->mockClient->method('get')->willReturn($mockResponse);

        $this->cdnManager->getManifestContent($video);
    }

    public function testGetManifestContentRequestException()
    {
        $this->expectException(InternalException::class);
        $this->expectExceptionMessage('Error communicating with CDN');

        $video = $this->createMock(Video::class);
        $video->method('getCdnId')->willReturn('videoCdnId');

        $this->mockClient->method('get')->willThrowException(new RequestException('Error', new \GuzzleHttp\Psr7\Request('GET', 'test')));

        $this->cdnManager->getManifestContent($video);
    }
}
