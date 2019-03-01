<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    /** @var EntityManagerInterface  */
    private $entityManager;

    /** @var UserPasswordEncoderInterface  */
    private $passwordEncoder;

    /**
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $encoder
    ) {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $encoder;
    }

    /**
     * @param User $user
     * @return string
     */
    public function createUser(User $user)
    {
        $user->setPassword(
            $this->passwordEncoder->encodePassword($user, $user->getPlainPassword())
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user->getUsername();
    }
}
