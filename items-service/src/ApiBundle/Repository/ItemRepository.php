<?php

namespace ApiBundle\Repository;

use ApiBundle\Entity\Item;
use ApiBundle\Repository\Traits\QueryBuilderTrait;
use Doctrine\ORM\QueryBuilder;

class ItemRepository extends EntityRepository
{
    use QueryBuilderTrait;

    /**
     * @return Item[]
     */
    public function getItems(): array
    {
        return $this->getResult($this->createQueryBuilder('i'));
    }

    /**
     * @param int $id
     * @return Item[]
     */
    public function getSellerItems(int $id): array
    {
        return $this->getResult($this->querySellerItems($id));
    }

    /**
     * @param int $id
     * @return QueryBuilder
     */
    private function querySellerItems(int $id): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->where($this->expr()->eq('c.sellerId', ':id'))
            ->setParameter('id', $id);
    }
}