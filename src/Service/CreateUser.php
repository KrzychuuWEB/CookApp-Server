<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Factory\UserFactory;
use App\Form\Model\UserFormModel;
use Doctrine\ORM\EntityManagerInterface;

class CreateUser
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;


    public function __construct(EntityManagerInterface $entity)
    {
        $this->entityManager = $entity;
    }

    public function create(UserFormModel $userFormModel): void
    {
        $factory = new UserFactory();
        $user = $factory->create($userFormModel);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
