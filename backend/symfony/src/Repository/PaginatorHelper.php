<?php

namespace App\Repository;

use App\DTO\PaginatorRequest;
use App\Helper\DTO\PaginatorResult;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

trait PaginatorHelper
{
    /**
     * @param QueryBuilder $qb
     * @param PaginatorRequest $paginatorRequest
     * @return PaginatorResult
     */
    private function getPaginatorResult(QueryBuilder $qb, PaginatorRequest $paginatorRequest): PaginatorResult
    {
        $qb->setMaxResults($paginatorRequest->getLimit())
            ->setFirstResult($paginatorRequest->getOffset());

        $paginator = new Paginator($qb);
        $totalItems = $paginator->count();

        return new PaginatorResult(iterator_to_array($paginator), $totalItems);
    }
}
