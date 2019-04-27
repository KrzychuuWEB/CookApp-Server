<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Permission;
use App\Entity\User;
use App\Repository\PermissionRepository;
use App\Service\PermissionService;
use App\Service\StringConverterService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class PermissionServiceTest extends TestCase
{
    /**
     * @var EntityManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManager;

    /**
     * @var PermissionRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $permissionRepository;

    /**
     * @var PermissionService
     */
    private $permissionService;

    /**
     * @var StringConverterService|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stringConverter;

    public function setUp()
    {
        $this->entityManager = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->permissionRepository = $this->getMockBuilder(PermissionRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->stringConverter = $this->getMockBuilder(StringConverterService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->permissionService = new PermissionService(
            $this->entityManager,
            $this->permissionRepository,
            $this->stringConverter
        );
    }

    public function tearDown()
    {
        $this->entityManager = null;
        $this->permissionRepository = null;
        $this->permissionService = null;
    }

    public function testCreatePermission()
    {
        $permission = new Permission();
        $permission->setName("ROLE_EXAMPLE");

        $this->stringConverter
            ->expects($this->at(0))
            ->method('setUppercase')
            ->with($permission->getName())
            ->willReturn("ROLE_EXAMPLE");
        $this->stringConverter
            ->expects($this->at(1))
            ->method('addPrefix')
            ->with("ROLE_EXAMPLE", "ROLE_")
            ->willReturn("ROLE_EXAMPLE");

        $permission->setName("ROLE_EXAMPLE");

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->willReturn($permission);
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $result = $this->permissionService->createPermission($permission);

        $this->assertEquals($result, $permission->getName());
    }

    public function testDeletePermission()
    {
        $permission = new Permission();
        $permission->setName("ROLE_EXAMPLE");

        $this->stringConverter
            ->expects($this->at(0))
            ->method('setUppercase')
            ->with($permission->getName())
            ->willReturn("ROLE_EXAMPLE");
        $this->stringConverter
            ->expects($this->at(1))
            ->method('addPrefix')
            ->with("ROLE_EXAMPLE", "ROLE_")
            ->willReturn("ROLE_EXAMPLE");

        $permission->setName("ROLE_EXAMPLE");

        $this->permissionRepository
            ->expects($this->once())
            ->method('findBy')
            ->with([
                'name' => "ROLE_EXAMPLE"
            ])
            ->willReturn([$permission]);

        $this->entityManager
            ->expects($this->once())
            ->method('remove')
            ->willReturn($permission);
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $result = $this->permissionService->deletePermission($permission);

        $this->assertTrue($result);
    }

    public function testDeletePermissionWithUsersHasThisPermission()
    {
        $user = new User();

        $permission = new Permission();
        $permission->setName("ROLE_EXAMPLE");
        $permission->addUser($user);

        $this->stringConverter
            ->expects($this->at(0))
            ->method('setUppercase')
            ->with($permission->getName())
            ->willReturn("ROLE_EXAMPLE");
        $this->stringConverter
            ->expects($this->at(1))
            ->method('addPrefix')
            ->with("ROLE_EXAMPLE", "ROLE_")
            ->willReturn("ROLE_EXAMPLE");

        $permission->setName("ROLE_EXAMPLE");

        $this->permissionRepository
            ->expects($this->once())
            ->method('findBy')
            ->with([
                'name' => "ROLE_EXAMPLE"
            ])
            ->willReturn([$permission]);

        $result = $this->permissionService->deletePermission($permission);

        $this->assertFalse($result);
    }

    public function testDeletePermissionWithBadPermissionInstance()
    {
        $permission = new Permission();
        $permission->setName("Example");

        $this->permissionRepository
            ->expects($this->once())
            ->method('findBy')
            ->willReturn(null);

        $result = $this->permissionService->deletePermission($permission);

        $this->assertNull($result);
    }

    public function testUpdatePermission()
    {
        $permission = new Permission();
        $permission->setName("ROLE_EXAMPLE");

        $this->permissionRepository
            ->expects($this->once())
            ->method('findBy')
            ->with([
                'name' => "ROLE_EXAMPLE"
            ])
            ->willReturn([$permission]);

        $permission->setName("ROLE_EXAMPLE_NEW");

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $result = $this->permissionService->updatePermission($permission, "ROLE_EXAMPLE");

        $this->assertTrue($result);
    }

    public function testUpdatePermissionWithBadInstance()
    {
        $permission = new Permission();
        $permission->setName("Example");

        $this->permissionRepository
            ->expects($this->once())
            ->method('findBy')
            ->willReturn(null);

        $result = $this->permissionService->updatePermission($permission, "ROLE_EXAMPLE");

        $this->assertNull($result);
    }

    public function testAddPermissionForUser()
    {
        $user = new User();

        $permission = new Permission();
        $permission->setName("ROLE_EXAMPLE");

        $this->stringConverter
            ->expects($this->at(0))
            ->method('setUppercase')
            ->with($permission->getName())
            ->willReturn("ROLE_EXAMPLE");
        $this->stringConverter
            ->expects($this->at(1))
            ->method('addPrefix')
            ->with("ROLE_EXAMPLE", "ROLE_")
            ->willReturn("ROLE_EXAMPLE");

        $permission->setName("ROLE_EXAMPLE");

        $this->permissionRepository
            ->expects($this->once())
            ->method('findBy')
            ->with([
                'name' => "ROLE_EXAMPLE",
            ])
            ->willReturn([$permission]);

        $result = $this->permissionService->addPermissionForUser([
            "permission" => "ROLE_EXAMPLE",
        ], $user);

        $this->assertTrue($result);
    }

    public function testAddPermissionForUserWithBadPermissionInstance()
    {
        $user = new User();

        $this->permissionRepository
            ->expects($this->once())
            ->method('findBy')
            ->willReturn(null);

        $result = $this->permissionService->addPermissionForUser([
            "permission" => "ROLE_EXAMPLE",
        ], $user);

        $this->assertNull($result);
    }

    public function testAddPermissionForUserWithUserHasThisPermission()
    {
        $user = new User();

        $permission = new Permission();
        $permission->setName("ROLE_USER");

        $this->stringConverter
            ->expects($this->at(0))
            ->method('setUppercase')
            ->with($permission->getName())
            ->willReturn("ROLE_USER");
        $this->stringConverter
            ->expects($this->at(1))
            ->method('addPrefix')
            ->with("ROLE_USER", "ROLE_")
            ->willReturn("ROLE_USER");

        $permission->setName("ROLE_USER");

        $this->permissionRepository
            ->expects($this->once())
            ->method('findBy')
            ->with([
                'name' => "ROLE_USER",
            ])
            ->willReturn([$permission]);

        $result = $this->permissionService->addPermissionForUser([
            "permission" => "ROLE_USER",
        ], $user);

        $this->assertFalse($result);
    }
}
