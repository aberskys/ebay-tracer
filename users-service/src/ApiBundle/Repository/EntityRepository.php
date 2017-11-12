<?php

namespace ApiBundle\Repository;

use Doctrine\ORM\QueryBuilder;

class EntityRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param QueryBuilder $qb
     * @return array
     */
    protected function getResult(QueryBuilder $qb): array
    {
        return $qb->getQuery()->getResult();
    }

    /**
     * @param QueryBuilder $qb
     * @return mixed
     */
    protected function getOneOrNullResult(QueryBuilder $qb)
    {
        return $qb->getQuery()->setMaxResults(1)->getOneOrNullResult();
    }
}