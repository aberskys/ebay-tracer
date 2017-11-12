<?php

namespace ApiBundle\Repository;

use ApiBundle\Entity\Order;
use ApiBundle\Entity\SaleOrder;
use ApiBundle\Repository\Traits\QueryBuilderTrait;
use Doctrine\ORM\QueryBuilder;

class OrderRepository extends EntityRepository
{
    use QueryBuilderTrait;

    const TYPE_BUYER = 'buyer';
    const TYPE_SELLER = 'seller';

    /**
     * @param int $userId
     * @return SaleOrder[]
     */
    public function getSellerOrders(int $userId): array
    {
        return $this->getResult($this->queryOrdersForUser($userId, self::TYPE_SELLER));
    }

    /**
     * @param int $userId
     * @return SaleOrder[]
     */
    public function getBuyerOrders(int $userId): array
    {
        return $this->getResult($this->queryOrdersForUser($userId, self::TYPE_BUYER));
    }

    /**
     * @param int $userId
     * @param string $type
     * @return QueryBuilder
     */
    private function queryOrdersForUser(int $userId, string $type): QueryBuilder
    {
        $qb = $this->createQueryBuilder('o');
        switch ($type) {
            case self::TYPE_SELLER:
                $qb->where($this->expr()->eq('o.sellerId', $userId));
                break;
            case self::TYPE_BUYER:
                $qb->where($this->expr()->eq('o.buyerId', $userId));
                break;
        }

        return $qb;
    }
}
