<?php

namespace ApiBundle\Controller\Traits;

trait DoctrineTrait
{
    /**
     * @param string $class
     * @return \Doctrine\ORM\EntityRepository
     */
    protected function repo($class)
    {
        return $this->getDoctrine()->getManager()->getRepository($class);
    }

    /**
     * @param array $entities
     * @return $this
     */
    protected function persist(...$entities)
    {
        $manager = $this->getDoctrine()->getManager();
        foreach ($entities as $entity) {
            $manager->persist($entity);
        }
        return $this;
    }

    /**
     * @param array $entities
     * @return $this
     */
    protected function remove(...$entities)
    {
        $manager = $this->getDoctrine()->getManager();

        foreach ($entities as $entity) {
            $manager->remove($entity);
        }

        return $this;
    }

    /**
     * @param string|null $class
     */
    protected function flush($class = null)
    {
        $this->getDoctrine()->getManager()->flush($class);
    }

}