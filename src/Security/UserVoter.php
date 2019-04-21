<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class UserVoter extends Voter
{
    const USER_EDIT = 'USER_EDIT';
    const USER_DELETE = 'USER_DELETE';

    /**
     * @var UserService $userService
     */
    private $userService;

    /**
     * @var Security
     */
    private $security;

    /**
     * AccountVoter constructor.
     *
     * @param UserService $userService
     * @param \Security $security
     */
    public function __construct(
        UserService $userService,
        Security $security
    ) {
        $this->userService = $userService;
        $this->security = $security;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject): bool
    {
        if (!in_array($attribute, [self::USER_EDIT, self::USER_DELETE])) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
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
            case self::USER_EDIT:
                return $this->canEdit($username, $user);
            case self::USER_DELETE:
                return $this->canDelete();
        }

        throw new \LogicException("This code should not be reached!");
    }

    /**
     * @param $username
     * @param $user
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
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
     * @return bool
     */
    private function canDelete(): bool
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $username
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return User|null
     */
    private function checkUserByUsername($username): ?User
    {
        return $this->userService->getUserByUsername($username);
    }
}
