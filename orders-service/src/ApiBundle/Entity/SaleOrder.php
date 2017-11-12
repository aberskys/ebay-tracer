<?php

namespace ApiBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\OrderRepository")
 * @ORM\Table
 */
class SaleOrder implements EntityInterface
{
    use EntityTrait;

    const STATUS_NEW = 0;
    const STATUS_PAID = 1;
    const STATUS_SHIPPED = 2;
    const STATUS_RECEIVED = 3;
    const STATUS_CANCELED = 4;

    /**
     * @var array
     */
    public static $sellerOrderStatuses = [
        'STATUS_SHIPPED' => 2,
        'STATUS_CANCELED' => 4,
    ];

    public static $buyerOrderStatuses = [
        'STATUS_NEW' => 0,
        'STATUS_PAID' => 1,
        'STATUS_RECEIVED' => 3,
        'STATUS_CANCELED' => 4,
    ];

    public static $orderStatuses = [
        'STATUS_NEW' => 0,
        'STATUS_PAID' => 1,
        'STATUS_SHIPPED' => 2,
        'STATUS_RECEIVED' => 3,
        'STATUS_CANCELED' => 4,
    ];

    /**
     * @var SaleOrderItem[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="SaleOrderItem", mappedBy="saleOrder")
     */
    private $saleOrderItems;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @Assert\NotNull(message="Buyer cannot be null")
     */
    private $buyerId;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @Assert\NotNull(message="Seller cannot be null")
     */
    private $sellerId;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $orderStatus;

    public function __construct()
    {
        $this->saleOrderItems = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getBuyerId(): ?int
    {
        return $this->buyerId;
    }

    /**
     * @param int $buyerId
     * @return SaleOrder
     */
    public function setBuyerId(int $buyerId): SaleOrder
    {
        $this->buyerId = $buyerId;

        return $this;
    }

    /**
     * @return int
     */
    public function getSellerId(): ?int
    {
        return $this->sellerId;
    }

    /**
     * @param int $sellerId
     * @return SaleOrder
     */
    public function setSellerId(int $sellerId): SaleOrder
    {
        $this->sellerId = $sellerId;

        return $this;
    }

    /**
     * @return int
     */
    public function getOrderStatus(): ?int
    {
        return $this->orderStatus;
    }

    /**
     * @param int $orderStatus
     * @return SaleOrder
     */
    public function setOrderStatus(int $orderStatus): SaleOrder
    {
        $this->orderStatus = $orderStatus;

        return $this;
    }

    /**
     * @return SaleOrderItem[]|ArrayCollection
     */
    public function getOrderItems()
    {
        return $this->saleOrderItems;
    }

    /**
     * @param SaleOrderItem[]|ArrayCollection $orderItems
     * @return SaleOrder
     */
    public function setOrderItems($orderItems): SaleOrder
    {
        $this->saleOrderItems = $orderItems;

        return $this;
    }

    /**
     * @param SaleOrderItem $item
     * @return SaleOrder
     */
    public function addOrderItem(SaleOrderItem $item): SaleOrder
    {
        $this->saleOrderItems->add($item);

        return $this;
    }
}