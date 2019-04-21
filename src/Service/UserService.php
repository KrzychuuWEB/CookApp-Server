<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    /** @var EntityManagerInterface  */
    private $entityManager;

    /** @var UserRepository */
    private $userRepository;

    /** @var UserPasswordEncoderInterface  */
    private $passwordEncoder;

    /** @var AccountService */
    private $accountService;

    /**
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @param UserPasswordEncoderInterface $encoder
     * @param AccountService $accountService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        UserPasswordEncoderInterface $encoder,
        AccountService $accountService
    ) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $encoder;
        $this->accountService = $accountService;
    }

    /**
     * @param User $user
     * @return string
     */
    public function createUser(User $user): string
    {
        $account = $this->accountService->returnNewAccountWithDefaultValues();

        $user->setAccount($account);
        $user->setPassword(
            $this->encodeUserPassword($user, $user->getPlainPassword())
        );

        $this->entityManager->persist($account);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user->getUsername();
    }

    /**
     * @param string $username
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return User|null
     *
     */
    public function getUserByUsername(string $username): ?User
    {
        $user = $this->userRepository->findUserByUsernameAndReturnOnlyActiveUser($username);

        if (count([$user]) > 1) {
            throw new NonUniqueResultException();
        }

        return $user;
    }

    public function deleteUser(User $user): bool
    {
        $user->setIsActive(false);

        $this->entityManager->flush();

        return true;
    }

    /**
     * @param array $payload
     * @param User $user
     *
     * @return bool|null
     */
    public function changePassword(array $payload, User $user): ?bool
    {
        if (!$this->validUserPassword($user, $payload['oldPassword'])) {
            return null;
        }

        $user->setPassword($this->encodeUserPassword($user, $payload['password']));
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param User $user
     * @param string $oldPassword
     *
     * @return bool|null
     */
    private function validUserPassword(User $user, string $oldPassword): ?bool
    {
        return $this->passwordEncoder->isPasswordValid($user, $oldPassword);
    }

    /**
     * @param User $user
     * @param string $password
     *
     * @return string|null
     */
    private function encodeUserPassword(User $user, string $password): ?string
    {
        return $this->passwordEncoder->encodePassword($user, $password);
    }
}
