<?php

namespace ApiBundle\Order;

class Order
{
    /**
     * @var int
     */
    private $buyerId;

    /**
     * @var int
     */
    private $sellerId;

    /**
     * @var int
     */
    private $status;

    public function __construct(array $orderInfo)
    {
        $this->buyerId = $orderInfo['buyer_id'];
        $this->sellerId = $orderInfo['seller_id'];
        $this->status = $orderInfo['order_status'];
    }

    /**
     * @return int
     */
    public function getBuyerId(): int
    {
        return $this->buyerId;
    }

    /**
     * @return int
     */
    public function getSellerId(): int
    {
        return $this->sellerId;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }
}