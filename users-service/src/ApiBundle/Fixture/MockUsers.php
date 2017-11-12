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

class MockUsers implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
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
        $faker = Factory::create();
        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);

            $user
                ->setFirstName($faker->firstName)
                ->setLastName($faker->lastName)
                ->setAddressLine1($faker->address)
                ->setCity($faker->city)
                ->setCountry($faker->country)
                ->setEmail($faker->email)
                ->setRole($i%2 == 0 ? User::ROLE_BUYER : User::ROLE_SELLER)
                ->setPassword($encoder->encodePassword($faker->word, sha1(md5(microtime()) . uniqid(rand()))))
                ->setZipCode($faker->postcode);
            $manager->persist($user);
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

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 2;
    }
}