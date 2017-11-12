<?php

namespace ApiBundle\Order;

class Item
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var int
     */
    private $qty;

    /**
     * @var float
     */
    private $price;

    /**
     * @param array $itemInfo
     */
    public function __construct(array $itemInfo)
    {
        $this->name = $itemInfo['name'];
        $this->description = $itemInfo['description'];
        $this->qty = $itemInfo['qty'];
        $this->price = $itemInfo['price'];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getQty(): int
    {
        return $this->qty;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param int $qtyToBuy
     * @return int
     */
    public function getRemainingQty(int $qtyToBuy): int
    {
        return $this->qty - $qtyToBuy;
    }
}
