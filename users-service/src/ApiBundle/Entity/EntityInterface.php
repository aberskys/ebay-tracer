<?php

namespace ApiBundle\Entity;


interface EntityInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return \DateTimeInterface
     */
    public function getCreatedAt();

    /**
     * @param \DateTimeInterface $createdAt
     */
    public function setCreatedAt(\DateTimeInterface $createdAt);

    /**
     * @return \DateTimeInterface
     */
    public function getUpdatedAt();

    /**
     * @param \DateTimeInterface|null $updatedAt
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt = null);
}