<?php

namespace App\Lib\Api;

use App\Lib\Api\Exception\ApiException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class ResponseEntity
{
    /**
     * @var SerializerInterface $serializer
     */
    private SerializerInterface $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Return a response with a message
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    public function withMessage(string $message, int $code = Response::HTTP_OK): JsonResponse
    {
        return $this->send($code, $this->getHeader($code) + [
                'payload' => [
                    'message' => $message
                ]
            ]);
    }

    /**
     * Return a response with a JSON data
     * @param array $payload
     * @param int $code
     * @return JsonResponse
     */
    public function withData(array $payload, int $code = Response::HTTP_OK): JsonResponse
    {
        $count = 0;
        if (isset($payload[0]) && is_array($payload[0])) {
            $count = count($payload);
        } elseif (!empty($payload)) {
            $count = 1;
        }

        return $this->send($code, $this->getHeader($code) + [
                'payload' => [
                    'count' => $count,
                    'data' => $payload,
                ]
            ]);
    }

    /**
     * Return a response with an exception
     * @param ApiException $ex
     * @return JsonResponse
     */
    public function withException(ApiException $ex): JsonResponse
    {
        return $this->send($ex->getCode(), $this->getHeader($ex->getCode(), 'error') + [
                'payload' => $ex->getTag()
                    ? ['tag' => $ex->getTag(), 'message' => $ex->getMessage()]
                    : ['message' => $ex->getMessage()]
            ]);
    }

    /**
     * Return a response with a JSON payload
     * @param int $code
     * @param string $status
     * @return array
     */
    private function getHeader(int $code, string $status = 'success'): array
    {
        return [
            'status' => $status,
            'code' => $code,
            'timestamp' => time()
        ];
    }

    /**
     * Send a response with a JSON payload
     * @param int $code
     * @param array $payload
     * @return JsonResponse
     */
    private function send(int $code, array $payload): JsonResponse
    {
        return new JsonResponse($this->serializer->serialize($payload, 'json'), $code, [], true);
    }
}
