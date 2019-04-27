<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\User;

class UserDTO
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $email;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var int
     */
    private $age;

    /**
     * @var string
     */
    private $hobby;

    /**
     * @var string
     */
    private $country;

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $aboutMe;

    /**
     * @var array
     */
    private $roles;

    /**
     * UserDTO constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->username = $user->getUsername();
        $this->email = $user->getEmail();
        $this->createdAt = $user->getCreatedAt();
        $this->roles = $user->getRoles();

        $this->firstName = $user->getAccount()->getFirstName();
        $this->lastName = $user->getAccount()->getLastName();
        $this->age = $user->getAccount()->getAge();
        $this->hobby = $user->getAccount()->getHobby();
        $this->country = $user->getAccount()->getCountry();
        $this->city = $user->getAccount()->getCity();
        $this->aboutMe = $user->getAccount()->getAboutMe();
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return int
     */
    public function getAge(): int
    {
        return $this->age;
    }

    /**
     * @return string
     */
    public function getHobby(): string
    {
        return $this->hobby;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getAboutMe(): string
    {
        return $this->aboutMe;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }
}
