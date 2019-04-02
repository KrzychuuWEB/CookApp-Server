<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
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
            $this->passwordEncoder->encodePassword($user, $user->getPlainPassword())
        );

        $this->entityManager->persist($account);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user->getUsername();
    }

    /**
     * @param string $username
     * @return User|null
     */
    public function getUserByUsername(string $username): ?User
    {
        return $this->userRepository->findOneBy([
            'username' => $username,
        ]);
    }
}
