<?php

namespace ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table
 */
class SaleOrderItem implements EntityInterface
{
    use EntityTrait;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="SaleOrder", inversedBy="saleOrderItems")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id", nullable=false)
     */
    private $saleOrder;

    /**
     * @var string
     * @ORM\Column(type="string", length=64)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $qty;

    /**
     * @var float
     * @ORM\Column(type="float")
     */
    private $price;

    public function __construct(SaleOrder $saleOrder)
    {
        $this->saleOrder = $saleOrder;
    }

    /**
     * @return SaleOrder
     */
    public function getSaleOrder(): SaleOrder
    {
        return $this->saleOrder;
    }

    /**
     * @param SaleOrder $saleOrder
     */
    public function setOrder(SaleOrder $saleOrder)
    {
        $this->saleOrder = $saleOrder;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): SaleOrderItem
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): SaleOrderItem
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return int
     */
    public function getQty(): ?int
    {
        return $this->qty;
    }

    /**
     * @param int $qty
     * @return $this
     */
    public function setQty(int $qty): SaleOrderItem
    {
        $this->qty = $qty;

        return $this;
    }

    /**
     * @return float
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @param float $price
     * @return $this
     */
    public function setPrice(float $price): SaleOrderItem
    {
        $this->price = $price;

        return $this;
    }
}