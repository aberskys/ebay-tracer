<?php

namespace ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\ItemRepository")
 * @ORM\Table
 */
class Item implements EntityInterface
{
    use EntityTrait;

    /**
     * @var string
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank(message="Name cannot be blank")
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
     * @Assert\NotNull(message="Quantity cannot be null")
     * @Assert\GreaterThanOrEqual(value="0", message="Value must be 0 or greater")
     */
    private $qty = 0;

    /**
     * @var float
     * @ORM\Column(type="float")
     * @Assert\NotNull(message="Price cannot be null")
     */
    private $price = 0.00;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Seller ID is not specified")
     */
    private $sellerId;

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Item
     */
    public function setName(string $name): Item
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Item
     */
    public function setDescription(string $description): Item
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return int
     */
    public function getQty(): int
    {
        return $this->qty;
    }

    /**
     * @param int $qty
     * @return Item
     */
    public function setQty(int $qty): Item
    {
        $this->qty = $qty;

        return $this;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     * @return Item
     */
    public function setPrice(float $price): Item
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getSellerId(): ?int
    {
        return $this->sellerId;
    }

    /**
     * @param int $sellerId
     * @return Item
     */
    public function setSellerId(int $sellerId): Item
    {
        $this->sellerId = $sellerId;

        return $this;
    }
}
