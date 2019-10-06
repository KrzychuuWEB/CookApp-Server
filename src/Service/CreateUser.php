<?php

declare(strict_types=1);

namespace App\Service;

use App\Factory\UserFactory;
use App\Form\Model\UserFormModel;
use Doctrine\ORM\EntityManagerInterface;

class CreateUser
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    private $userFactory;

    public function __construct(EntityManagerInterface $entity, UserFactory $userFactory)
    {
        $this->entityManager = $entity;
        $this->userFactory = $userFactory;
    }

    public function create(UserFormModel $userFormModel): void
    {
        $user = $this->userFactory->create($userFormModel);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
