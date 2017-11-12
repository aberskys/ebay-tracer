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
}