<?php

namespace App\Repository;

use App\DTO\Filter\FilterRequest;
use App\Helper\DTO\OrderBy;
use App\Helper\DTO\SortBy;
use Doctrine\ORM\QueryBuilder;

interface FilterSortInterface
{
    /**
     * @param FilterRequest|null $filter
     * @param QueryBuilder $qb
     * @return void
     */
    public function applyFilter(?FilterRequest $filter, QueryBuilder $qb): void;

    /**
     * @param SortBy $sort
     * @param OrderBy $order
     * @param QueryBuilder $qb
     * @return void
     */
    public function applySort(SortBy $sort, OrderBy $order, QueryBuilder $qb): void;
}