<?php

namespace ApiBundle\Fixture;

use ApiBundle\Entity\SaleOrder;
use ApiBundle\Entity\User;
use ApiBundle\Entity\SaleOrderItem;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MockOrders implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        if (!in_array($this->container->getParameter('kernel.environment'), ['dev', 'test'])) {
            return; // only for dev environment
        }

        $faker = Factory::create();
        $ordersCount = $faker->numberBetween(1,10);

        for($i = 0; $i < $ordersCount; $i++) {
            $order = new SaleOrder();
            $order->setBuyerId(2)
                ->setSellerId(1)
                ->setOrderStatus($faker->numberBetween(1,4));

            $orderItem = new SaleOrderItem($order);
            $orderItem->setName($faker->word)
                ->setDescription($faker->text())
                ->setPrice($faker->randomFloat())
                ->setQty($faker->randomNumber());

            $manager->persist($orderItem);
            $manager->persist($order);
        }

        $manager->flush();
    }

    /**
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}