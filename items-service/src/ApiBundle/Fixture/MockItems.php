<?php

namespace ApiBundle\Fixture;

use ApiBundle\Entity\Item;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MockItems implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        if (!in_array($this->container->getParameter('kernel.environment'), ['dev', 'test'])) {
            return; // only for dev environment
        }

        $faker = Factory::create();
        $itemsCount = 10;

        for($i = 0; $i < $itemsCount; $i++) {
            $item = new Item();
            $item->setName(ucfirst($faker->word))
                ->setDescription(ucfirst($faker->text()))
                ->setQty($faker->numberBetween(500, 2000))
                ->setPrice($faker->randomFloat())
                ->setSellerId(1);

                $manager->persist($item);
        }

        $manager->flush();
    }

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}