<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\User;
use App\Form\Model\UserFormModel;

class UserFactory
{
    public function create(UserFormModel $userFormModel): User
    {
        $user = new User();
        $user->setUsername($userFormModel->getUsername());
        $user->setEmail($userFormModel->getEmail());
        $user->setPassword($userFormModel->getPassword());
        $user->setIsActive(true);

        return $user;
    }
}
