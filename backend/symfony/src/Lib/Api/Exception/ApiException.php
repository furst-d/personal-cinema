<?php

namespace App\Lib\Api\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class ApiException extends Exception
{
    /**
     * @var string|null $tag
     */
    protected ?string $tag;

    /**
     * @param string $message
     * @param int $code
     * @param string|null $tag
     */
    public function __construct(string $message, int $code = Response::HTTP_INTERNAL_SERVER_ERROR, string $tag = null)
    {
        parent::__construct($message, $code);
        $this->tag = $tag;
    }

    /**
     * @return string|null
     */
    public function getTag(): ?string
    {
        return $this->tag;
    }
}
