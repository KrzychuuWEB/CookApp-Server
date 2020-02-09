<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\ApiException;
use App\Factory\UserFactory;
use App\Form\Model\UserFormModel;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

class CreateUserService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * CreateUserService constructor.
     * @param EntityManagerInterface $entity
     * @param UserFactory $userFactory
     * @param UserRepository $userRepository
     */
    public function __construct(EntityManagerInterface $entity, UserFactory $userFactory, UserRepository $userRepository)
    {
        $this->entityManager = $entity;
        $this->userFactory = $userFactory;
        $this->userRepository = $userRepository;
    }

    /**
     * @param UserFormModel $userFormModel
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createUser(UserFormModel $userFormModel): void
    {
        try {
            $user = $this->userFactory->create($userFormModel);
            $this->userRepository->save($user);
        } catch (RuntimeException $exception) {
            throw new ApiException("User is not created!", Response::HTTP_BAD_REQUEST);
        }
    }
}
