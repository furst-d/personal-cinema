<?php

namespace App\Exception;

use Exception;

abstract class ApiException extends Exception
{
    /**
     * @var string|null $tag
     */
    protected ?string $tag;

    /**
     * @var array
     */
    protected array $details;

    /**
     * @param string $message
     * @param int $code
     * @param array $details
     * @param string|null $tag
     */
    public function __construct(string $message, int $code, array $details = [], string $tag = null)
    {
        parent::__construct($message, $code);
        $this->details = $details;
        $this->tag = $tag;
    }

    /**
     * @return string|null
     */
    public function getTag(): ?string
    {
        return $this->tag;
    }

    /**
     * @return array
     */
    public function getDetails(): array
    {
        return $this->details;
    }
}
