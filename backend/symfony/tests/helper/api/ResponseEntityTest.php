<?php

namespace App\Tests\Helper\Api;

use App\Helper\Api\Exception\ApiException;
use App\Helper\Api\ResponseEntity;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class ResponseEntityTest extends TestCase
{
    private const TEST_MESSAGE = 'Test message';
    private const TEST_CODE = Response::HTTP_OK;

    private $serializer;
    private $responseEntity;

    protected function setUp(): void
    {
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->responseEntity = new ResponseEntity($this->serializer);
    }

    public function testWithMessage()
    {
        $payload = [
            'status' => 'success',
            'code' => self::TEST_CODE,
            'timestamp' => time(),
            'payload' => [
                'message' => self::TEST_MESSAGE
            ]
        ];

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with($payload, 'json')
            ->willReturn(json_encode($payload));

        $response = $this->responseEntity->withMessage(self::TEST_MESSAGE, self::TEST_CODE);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(self::TEST_CODE, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode($payload), $response->getContent());
    }

    public function testWithData()
    {
        $data = ['key' => 'value'];
        $payload = [
            'status' => 'success',
            'code' => self::TEST_CODE,
            'timestamp' => time(),
            'payload' => [
                'count' => 1,
                'data' => $data
            ]
        ];

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with($payload, 'json')
            ->willReturn(json_encode($payload));

        $response = $this->responseEntity->withData($data, self::TEST_CODE);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(self::TEST_CODE, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode($payload), $response->getContent());
    }

    public function testWithException()
    {
        $exception = new CustomApiException(self::TEST_MESSAGE, Response::HTTP_BAD_REQUEST, ['detail' => 'info'], 'test_tag');

        $payload = [
            'status' => 'error',
            'code' => Response::HTTP_BAD_REQUEST,
            'timestamp' => time(),
            'payload' => [
                'message' => self::TEST_MESSAGE,
                'tag' => 'test_tag',
                'details' => ['detail' => 'info']
            ]
        ];

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with($payload, 'json')
            ->willReturn(json_encode($payload));

        $response = $this->responseEntity->withException($exception);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(json_encode($payload), $response->getContent());
    }

    public function testGetCountWithObject()
    {
        $data = new \stdClass();
        $result = $this->invokeMethod($this->responseEntity, 'getCount', [$data]);
        $this->assertEquals(1, $result);
    }

    public function testGetCountWithAssociativeArray()
    {
        $data = ['key' => 'value'];
        $result = $this->invokeMethod($this->responseEntity, 'getCount', [$data]);
        $this->assertEquals(1, $result);
    }

    public function testGetCountWithListArray()
    {
        $data = ['value1', 'value2'];
        $result = $this->invokeMethod($this->responseEntity, 'getCount', [$data]);
        $this->assertEquals(2, $result);
    }

    private function invokeMethod($object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}

class CustomApiException extends ApiException
{
    public function __construct(string $message, int $code, array $details = [], string $tag = null)
    {
        parent::__construct($message, $code, $details, $tag);
    }
}
