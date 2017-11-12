<?php

namespace ApiBundle\Repository\Traits;

use Doctrine\ORM\Query\Expr;

trait QueryBuilderTrait
{
    /**
     * @return Expr
     */
    public function expr(): Expr
    {
        return $this->getEntityManager()->getExpressionBuilder();
    }
}
