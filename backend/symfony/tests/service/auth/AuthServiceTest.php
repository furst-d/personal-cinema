<?php

namespace App\Tests\Service\Auth;

use App\Entity\Video\Video;
use App\Exception\NotFoundException;
use App\Exception\UnauthorizedException;
use App\Helper\Jwt\JwtUsage;
use App\Service\Auth\AuthService;
use App\Service\Cdn\CdnService;
use App\Service\Jwt\JwtService;
use App\Service\Video\VideoService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class AuthServiceTest extends TestCase
{
    private $authService;
    private $mockCdnService;
    private $mockVideoService;
    private $mockJwtService;

    protected function setUp(): void
    {
        $this->mockCdnService = $this->createMock(CdnService::class);
        $this->mockVideoService = $this->createMock(VideoService::class);
        $this->mockJwtService = $this->createMock(JwtService::class);

        $this->authService = new AuthService(
            $this->mockCdnService,
            $this->mockVideoService,
            $this->mockJwtService
        );
    }

    public function testAuthCdnSuccess()
    {
        $request = new Request(['token' => 'validToken']);

        $this->mockCdnService->expects($this->once())
            ->method('getCdnCallbackKey')
            ->willReturn('validToken');

        $this->authService->authCdn($request);

        $this->assertTrue(true); // If no exception is thrown, the test passes
    }

    public function testAuthCdnTokenMissing()
    {
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('Token is required');

        $request = new Request();

        $this->authService->authCdn($request);
    }

    public function testAuthCdnInvalidToken()
    {
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('Invalid token');

        $request = new Request(['token' => 'invalidToken']);

        $this->mockCdnService->expects($this->once())
            ->method('getCdnCallbackKey')
            ->willReturn('validToken');

        $this->authService->authCdn($request);
    }

    public function testAuthVideoSuccess()
    {
        $request = new Request(['token' => 'validToken']);
        $decodedToken = ['video_id' => 1];
        $video = $this->createMock(Video::class);

        $this->mockJwtService->expects($this->once())
            ->method('decodeToken')
            ->with('validToken', JwtUsage::USAGE_VIDEO_ACCESS)
            ->willReturn($decodedToken);

        $this->mockVideoService->expects($this->once())
            ->method('getVideoById')
            ->with(1)
            ->willReturn($video);

        $result = $this->authService->authVideo($request);

        $this->assertInstanceOf(Video::class, $result);
    }

    public function testAuthVideoTokenMissing()
    {
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('Token is required');

        $request = new Request();

        $this->authService->authVideo($request);
    }

    public function testAuthVideoInvalidToken()
    {
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('Invalid token');

        $request = new Request(['token' => 'invalidToken']);

        $this->mockJwtService->expects($this->once())
            ->method('decodeToken')
            ->with('invalidToken', JwtUsage::USAGE_VIDEO_ACCESS)
            ->willThrowException(new UnauthorizedException('Invalid token'));

        $this->authService->authVideo($request);
    }

    public function testAuthVideoNotFound()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Video not found');

        $request = new Request(['token' => 'validToken']);
        $decodedToken = ['video_id' => 1];

        $this->mockJwtService->expects($this->once())
            ->method('decodeToken')
            ->with('validToken', JwtUsage::USAGE_VIDEO_ACCESS)
            ->willReturn($decodedToken);

        $this->mockVideoService->expects($this->once())
            ->method('getVideoById')
            ->with(1)
            ->willThrowException(new NotFoundException('Video not found'));

        $this->authService->authVideo($request);
    }
}
