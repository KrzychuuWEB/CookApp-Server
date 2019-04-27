<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Permission;
use App\Entity\User;
use App\Repository\PermissionRepository;
use Doctrine\ORM\EntityManagerInterface;

class PermissionService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var PermissionRepository
     */
    private $permissionRepository;

    /**
     * @var StringConverterService
     */
    private $stringConverter;

    /**
     * PermissionService constructor.
     *
     * @param EntityManagerInterface $entity
     * @param PermissionRepository $permissionRepository
     * @param StringConverterService $stringConverter
     */
    public function __construct(
        EntityManagerInterface $entity,
        PermissionRepository $permissionRepository,
        StringConverterService $stringConverter
    ) {
        $this->entityManager = $entity;
        $this->permissionRepository = $permissionRepository;
        $this->stringConverter = $stringConverter;
    }

    /**
     * @param Permission $permission
     *
     * @return string|null
     */
    public function createPermission(Permission $permission): ?string
    {
        $name = $this->stringConverter->setUppercase($permission->getName());
        $name = $this->stringConverter->addPrefix($name, "ROLE_");

        $permission->setName($name);

        $this->entityManager->persist($permission);
        $this->entityManager->flush();

        return $permission->getName();
    }

    /**
     * @param Permission $permission
     *
     * @return bool|null
     */
    public function deletePermission(Permission $permission): ?bool
    {
        $name = $this->stringConverter->setUppercase($permission->getName());
        $name = $this->stringConverter->addPrefix($name, "ROLE_");

        $permissionRepository = $this->permissionRepository->findBy(['name' => $name])[0];

        if (!$permissionRepository instanceof Permission) {
            return null;
        }

        if (count($permissionRepository->getUser()) > 0) {
            return false;
        }

        $this->entityManager->remove($permissionRepository);
        $this->entityManager->flush();

        return true;
    }

    public function updatePermission(Permission $permission, $permissionName)
    {
        $name = $this->stringConverter->setUppercase($permission->getName());
        $name = $this->stringConverter->addPrefix($name, "ROLE_");

        $permissionRepository = $this->permissionRepository->findBy(['name' => $permissionName])[0];

        if (!$permissionRepository instanceof Permission) {
            return null;
        }

        $permissionRepository->setName($name);

        $this->entityManager->flush();

        return true;
    }

    /**
     * @param array $data
     * @param User $user
     *
     * @return bool|null
     */
    public function addPermissionForUser(array $data, User $user): ?bool
    {
        $name = $this->stringConverter->setUppercase($data['permission']);
        $name = $this->stringConverter->addPrefix($name, "ROLE_");

        $permission = $this->permissionRepository->findBy(['name' => $name])[0];

        if (!$permission instanceof Permission) {
            return null;
        }

        if ($this->checkUserWhetherHasBeenPermission($user, $name)) {
            return false;
        }

        $user->addPermission($permission);

        $this->entityManager->persist($permission);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param array $data
     * @param User $user
     *
     * @return bool|null
     */
    public function deletePermissionForUser(array $data, User $user)
    {
        $name = $this->stringConverter->setUppercase($data['permission']);
        $name = $this->stringConverter->addPrefix($name, "ROLE_");

        $permission = $this->permissionRepository->findBy(['name' => $name])[0];

        if (!$permission instanceof Permission) {
            return null;
        }

        if (!$this->checkUserWhetherHasBeenPermission($user, $name)) {
            return false;
        }

        $user->removePermission($permission);

        $this->entityManager->persist($permission);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param User $user
     * @param string $newPermission
     *
     * @return bool
     */
    private function checkUserWhetherHasBeenPermission(User $user, string $newPermission): bool
    {
        foreach ($user->getRoles() as $role) {
            if ($role === $newPermission) {
                return true;
            }
        }

        return false;
    }
}
