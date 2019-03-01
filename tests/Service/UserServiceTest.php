<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserServiceTest extends TestCase
{
    /** @var EntityManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $entityManagerInterface;

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /** @var UserService */
    private $userService;

    protected function setUp()
    {
        $this->entityManagerInterface = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->passwordEncoder = $this->createMock(UserPasswordEncoderInterface::class);
        $this->passwordEncoder->method('encodePassword')
            ->willReturn("ExamplePassword");

        $this->userService = new UserService($this->entityManagerInterface, $this->passwordEncoder);
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

        $this->assertEquals($result, $user->getUsername());
    }
}