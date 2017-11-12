<?php

namespace ApiBundle\Repository;

use ApiBundle\Entity\User;
use ApiBundle\Repository\Traits\QueryBuilderTrait;
use Doctrine\ORM\QueryBuilder;

class UserRepository extends EntityRepository
{
    use QueryBuilderTrait;

    /**
     * @return User[]
     */
    public function getUsers(): array
    {
        return $this->getResult(
            $this->createQueryBuilder('u')
        );
    }

    /**
     * @return User[]
     */
    public function getSellers(): array
    {
        return $this->getResult($this->querySellers());
    }

    /**
     * @param int $id
     * @return User|null
     */
    public function getUserById(int $id): ?User
    {
        return $this->getOneOrNullResult($this->queryUserById($id));
    }

    /**
     * @return QueryBuilder
     */
    private function querySellers(): QueryBuilder
    {
        return $this->createQueryBuilder('u')
            ->where($this->expr()->eq('u.role', ':role'))
            ->setParameter('role', User::ROLE_SELLER);
    }

    /**
     * @param int $id
     * @return QueryBuilder
     */
    private function queryUserById(int $id): QueryBuilder
    {
        return $this->createQueryBuilder('u')
            ->where($this->expr()->eq('u.id', ':id'))
            ->setParameter('id', $id);
    }
}
