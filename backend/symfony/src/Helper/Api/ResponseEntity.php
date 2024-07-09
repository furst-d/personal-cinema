<?php

namespace App\Helper\Api;

use App\Exception\ApiException;
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
     * @param object|array $payload
     * @param int $code
     * @return JsonResponse
     */
    public function withData(object|array $payload, int $code = Response::HTTP_OK): JsonResponse
    {
        return $this->send($code, $this->getHeader($code) + [
                'payload' => [
                    'count' => $this->getCount($payload),
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
        $payload = ['message' => $ex->getMessage()];

        if ($ex->getTag()) {
            $payload['tag'] = $ex->getTag();
        }

        if ($ex->getDetails()) {
            $payload['details'] = $ex->getDetails();
        }

        return $this->send($ex->getCode(), $this->getHeader($ex->getCode(), 'error') + [
                'payload' => $payload
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
     * @param object|array $payload
     * @return int
     */
    private function getCount(object|array $payload): int
    {
        // Object or an associative array (custom payloads)
        if (is_object($payload) || !array_is_list($payload)) {
            return 1;
        }

        return count($payload);
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
