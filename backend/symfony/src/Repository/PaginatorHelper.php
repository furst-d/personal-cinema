<?php

namespace App\Repository;

use App\Helper\Paginator\PaginatorResult;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

trait PaginatorHelper
{
    /**
     * @param QueryBuilder $qb
     * @param int|null $limit
     * @param int|null $offset
     * @return PaginatorResult
     */
    private function getPaginatorResult(QueryBuilder $qb, ?int $limit, ?int $offset): PaginatorResult
    {
        if (!is_null($limit) && !is_null($offset)) {
            $qb->setMaxResults($limit)
                ->setFirstResult($offset);
        }

        $paginator = new Paginator($qb);
        $totalItems = $paginator->count();

        return new PaginatorResult(iterator_to_array($paginator), $totalItems);
    }
}
