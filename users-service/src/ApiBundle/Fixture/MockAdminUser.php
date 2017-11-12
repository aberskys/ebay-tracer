<?php

namespace ApiBundle\Fixture;

use ApiBundle\Entity\User;
use ApiBundle\Entity\SaleOrderItem;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MockAdminUser implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
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
        $user = new User();
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);

        $user
            ->setFirstName('System')
            ->setLastName('Administrator')
            ->setAddressLine1('Address 1-2')
            ->setCity('Demo city')
            ->setCountry('Lithuania')
            ->setEmail('admin@demoapp.dev')
            ->setRole(User::ROLE_ADMIN)
            ->setPassword($encoder->encodePassword('MyS3cretPassw0rd', sha1(md5(microtime()) . uniqid(rand()))))
            ->setZipCode('123458');

        $manager->persist($user);

        $manager->flush();
    }

    /**
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 1;
    }
}