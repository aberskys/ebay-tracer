<?php

namespace ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="ApiBundle\Repository\UserRepository")
 * @ORM\Table
 * @UniqueEntity("email")
 */
class User implements EntityInterface
{
    use EntityTrait;

    const ROLE_ADMIN = 0;
    const ROLE_BUYER = 1;
    const ROLE_SELLER = 2;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message="Email address cannot be empty.")
     * @Assert\Email(message="Email address is not valid.")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * Plain password. Used for model validation. Must not be persisted.
     *
     * @Assert\NotBlank(message="Password cannot be empty")
     * @Assert\Length(
     *   min=8,
     *   max=4096,
     *   minMessage="Password is too short",
     *   maxMessage="Password is too long"
     * )
     */

    private $plainPassword;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32)
     * @Assert\NotBlank(message="First name must not be empty")
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32)
     * @Assert\NotBlank(message="Last name must not be empty")
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Address cannot be empty")
     */
    private $addressLine1;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $addressLine2;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=16)
     * @Assert\NotBlank(message="Zip code cannot be empty")
     */
    private $zipCode;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32)
     *
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=128)
     */
    private $country;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint")
     */
    private $role = self::ROLE_BUYER;

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail(string $email = null): User
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword(string $password): User
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param mixed $plainPassword
     * @return $this
     */
    public function setPlainPassword($plainPassword = null)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return $this
     */
    public function setFirstName(string $firstName = null)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     * @return $this
     */
    public function setLastName(string $lastName = null)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddressLine1(): ?string
    {
        return $this->addressLine1;
    }

    /**
     * @param string $addressLine1
     * @return $this
     */
    public function setAddressLine1(string $addressLine1 = null)
    {
        $this->addressLine1 = $addressLine1;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddressLine2(): ?string
    {
        return $this->addressLine2;
    }

    /**
     * @param string|null $addressLine2
     * @return $this
     */
    public function setAddressLine2(string $addressLine2 = null)
    {
        $this->addressLine2 = $addressLine2;

        return $this;
    }

    /**
     * @return string
     */
    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    /**
     * @param string $zipCode
     * @return $this
     */
    public function setZipCode(string $zipCode = null)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string $city
     * @return $this
     */
    public function setCity(string $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @param string $country
     * @return $this
     */
    public function setCountry(string $country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * @return bool
     */
    public function isSeller(): bool
    {
        return $this->role === self::ROLE_SELLER;
    }

    /**
     * @param int $role
     * @return User
     */
    public function setRole(int $role): User
    {
        $this->role = $role;

        return $this;
    }
}