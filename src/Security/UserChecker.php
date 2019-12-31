<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        if ($user->getIsActive() < 1) {
            throw new AccountExpiredException("Account is expired.");
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        return;
    }
}
