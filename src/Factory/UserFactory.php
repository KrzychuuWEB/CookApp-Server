<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\User;
use App\Form\Model\UserFormModel;
use App\Service\PasswordEncoder;

class UserFactory
{
    /**
     * @var PasswordEncoder
     */
    private $passwordEncoder;

    /**
     * UserFactory constructor.
     * @param PasswordEncoder $encoder
     */
    public function __construct(PasswordEncoder $encoder)
    {
        $this->passwordEncoder = $encoder;
    }

    public function create(UserFormModel $userFormModel): User
    {
        $user = new User();
        $user->setUsername($userFormModel->getUsername());
        $user->setEmail($userFormModel->getEmail());
        $user->setPassword(
            $this->passwordEncoder->hashPassword($user, $userFormModel->getPassword())
        );
        $user->setIsActive(true);

        return $user;
    }
}
