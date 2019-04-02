<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Accounts;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\AccountService;
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

    /** @var AccountService */
    private $accountService;

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
        $this->accountService = $this->getMockBuilder(AccountService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->userService = new UserService(
            $this->entityManagerInterface,
            $this->userRepository,
            $this->passwordEncoder,
            $this->accountService
        );
    }

    protected function tearDown()
    {
        $this->entityManagerInterface = null;
        $this->passwordEncoder = null;
    }

    public function testCreateUser()
    {
        $account = new Accounts();
        $account->setFirstName("ExampleFirstName");

        $this->accountService
            ->expects($this->once())
            ->method('returnNewAccountWithDefaultValues')
            ->willReturn($account);

        $user = new User();
        $user->setUsername("ExampleUsername");
        $user->setPlainPassword("ExamplePassword");
        $user->setEmail("Example@Email.com");
        $user->setAccount($account);

        $this->entityManagerInterface
            ->expects($this->at(0))
            ->method('persist')
            ->with($account);
        $this->entityManagerInterface
            ->expects($this->at(1))
            ->method('persist')
            ->with($user);
        $this->entityManagerInterface
            ->expects($this->once())
            ->method('flush');

        $result = $this->userService->createUser($user);

        $this->assertEquals("ExampleUsername", $result);
    }

    public function testGetUserByUsername()
    {
        $user = new User();

        $this->userRepository
            ->expects($this->any())
            ->method('findOneBy')
            ->with([
                'username' => 'Admin',
            ])
            ->willReturn($user);

        $result = $this->userService->getUserByUsername("Admin");

        $this->assertEquals($user, $result);
    }
}
