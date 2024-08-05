<?php

namespace App\Helper\DTO;

/**
 * @template T
 */
class PaginatorResult
{
    /**
     * @var array<T>
     */
    private array $data;

    /**
     * @var int
     */
    private int $total;

    /**
     * @param array<T> $data
     * @param int $total
     */
    public function __construct(array $data, int $total)
    {
        $this->data = $data;
        $this->total = $total;
    }

    /**
     * @return array<T>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }
}
