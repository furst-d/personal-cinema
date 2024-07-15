<?php

namespace listener;

use App\Entity\Account\Account;
use App\Exception\UnauthorizedException;
use App\Helper\Api\ResponseEntity;
use App\Helper\Jwt\JwtUsage;
use App\Listener\TokenValidatorListener;
use App\Service\Account\TokenUserProvider;
use App\Service\Jwt\JwtService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class TokenValidatorListenerTest extends TestCase
{
    private $jwtService;
    private $responseEntity;
    private $userProvider;
    private $listener;

    private const MOCK_ROUTE = '/v1/personal/some-route';

    protected function setUp(): void
    {
        $this->jwtService = $this->createMock(JwtService::class);
        $this->responseEntity = $this->createMock(ResponseEntity::class);
        $this->userProvider = $this->createMock(TokenUserProvider::class);
        $this->listener = new TokenValidatorListener($this->jwtService, $this->responseEntity, $this->userProvider);
    }

    public function testOnKernelRequestWithValidToken()
    {
        $request = new Request([], [], [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer validToken']);
        $request->attributes->set('_route', 'app_route');
        $request->server->set('REQUEST_URI', self::MOCK_ROUTE);
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new RequestEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST);

        $decodedToken = ['user_id' => 1];
        $user = new Account('test@example.com', 'password', 'salt');

        $this->jwtService->expects($this->once())
            ->method('decodeToken')
            ->with('validToken', JwtUsage::USAGE_API_ACCESS)
            ->willReturn($decodedToken);

        $this->userProvider->expects($this->once())
            ->method('loadUserByIdentifier')
            ->with(1)
            ->willReturn($user);

        $this->listener->onKernelRequest($event);

        $this->assertSame($user, $request->attributes->get('account'));
    }

    public function testOnKernelRequestWithInvalidToken()
    {
        $request = new Request([], [], [], [], [], ['HTTP_AUTHORIZATION' => 'Bearer invalidToken']);
        $request->attributes->set('_route', 'app_route');
        $request->server->set('REQUEST_URI', self::MOCK_ROUTE);
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new RequestEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST);

        $this->jwtService->expects($this->once())
            ->method('decodeToken')
            ->with('invalidToken', JwtUsage::USAGE_API_ACCESS)
            ->willThrowException(new UnauthorizedException('Invalid or expired token.'));

        $response = new JsonResponse(['status' => 'error', 'code' => 401, 'timestamp' => time(), 'payload' => ['message' => 'Invalid or expired token.']], Response::HTTP_UNAUTHORIZED);
        $this->responseEntity->expects($this->once())
            ->method('withException')
            ->with($this->isInstanceOf(UnauthorizedException::class))
            ->willReturn($response);

        $this->listener->onKernelRequest($event);

        $this->assertSame($response, $event->getResponse());
        $this->assertNull($request->attributes->get('account'));
    }

    public function testOnKernelRequestWithoutToken()
    {
        $request = new Request();
        $request->attributes->set('_route', 'app_route');
        $request->server->set('REQUEST_URI', self::MOCK_ROUTE);
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new RequestEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST);

        $response = new JsonResponse(['status' => 'error', 'code' => 401, 'timestamp' => time(), 'payload' => ['message' => 'Token not provided.']], Response::HTTP_UNAUTHORIZED);
        $this->responseEntity->expects($this->once())
            ->method('withException')
            ->with($this->isInstanceOf(UnauthorizedException::class))
            ->willReturn($response);

        $this->listener->onKernelRequest($event);

        $this->assertSame($response, $event->getResponse());
        $this->assertNull($request->attributes->get('account'));
    }
}
