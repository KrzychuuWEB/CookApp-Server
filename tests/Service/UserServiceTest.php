<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserServiceTest extends TestCase
{
    /** @var EntityManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $entityManagerInterface;

    /** @var UserRepository|\PHPUnit_Framework_MockObject_MockObject */
    private $userRepository;

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /** @var UserService */
    private $userService;

    protected function setUp()
    {
        $this->entityManagerInterface = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->userRepository = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->passwordEncoder = $this->createMock(UserPasswordEncoderInterface::class);
        $this->passwordEncoder->method('encodePassword')
            ->willReturn("ExamplePassword");

        $this->userService = new UserService(
            $this->entityManagerInterface,
            $this->userRepository,
            $this->passwordEncoder
        );
    }

    protected function tearDown()
    {
        $this->entityManagerInterface = null;
        $this->passwordEncoder = null;
    }

    public function testCreateUser()
    {
        $user = new User();
        $user->setUsername("ExampleUsername");
        $user->setPlainPassword("ExamplePassword");
        $user->setEmail("Example@Email.com");

        $this->entityManagerInterface
            ->expects($this->once())
            ->method('persist')
            ->with($user);
        $this->entityManagerInterface
            ->expects($this->once())
            ->method('flush');

        $result = $this->userService->createUser($user);

        $this->assertEquals("ExampleUsername", $result);
    }

    /**
     * @dataProvider getOneUserByIdDataProvider
     *
     * @param $id
     * @param $user
     * @param $expected
     */
    public function testGetUserById($id, $user, $expected)
    {
        $this->userRepository
            ->expects($this->any())
            ->method('find')
            ->with($id)
            ->willReturn($user);

        $result = $this->userService->getUserById($id);

        $this->assertEquals($expected, $result);
    }

    public function getOneUserByIdDataProvider()
    {
        $id0 = 0;
        $user0 = new User();
        $expected0 = $user0;

        $id1 = 1;
        $user1 = new User();
        $expected1 = $user1;

        return [
          [$id0, $user0, $expected0],
          [$id1, $user1, $expected1],
        ];
    }
}
