<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AccountVoter extends Voter
{
    const ACCOUNT_EDIT = 'ACCOUNT_EDIT';

    /**
     * @var UserService $userService
     */
    private $userService;

    /**
     * AccountVoter constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject): bool
    {
        if (!in_array($attribute, [self::ACCOUNT_EDIT])) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        $username = $subject;

        switch ($attribute) {
            case self::ACCOUNT_EDIT:
                return $this->canEdit($username, $user);
        }

        throw new \LogicException("This code should not be reached!");
    }

    /**
     * @param $username
     * @param $user
     *
     * @return bool
     */
    private function canEdit($username, $user): bool
    {
        if ($this->checkUserByUsername($username)) {
            return strtolower($user->getUsername()) === strtolower($username);
        } else {
            return false;
        }
    }

    /**
     * @param $username
     *
     * @return User|null
     */
    private function checkUserByUsername($username): ?User
    {
        return $this->userService->getUserByUsername($username);
    }
}
