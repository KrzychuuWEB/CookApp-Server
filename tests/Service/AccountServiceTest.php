<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Accounts;
use App\Repository\AccountsRepository;
use App\Service\AccountService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class AccountServiceTest extends TestCase
{
    /**@var EntityManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $entityManager;

    /**@var AccountsRepository|\PHPUnit_Framework_MockObject_MockObject */
    private $accountsRepository;

    /**@var AccountService */
    private $accountService;

    protected function setUp()
    {
        $this->entityManager = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->accountsRepository = $this->getMockBuilder(AccountsRepository::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->accountService = new AccountService(
            $this->entityManager,
            $this->accountsRepository
        );
    }

    protected function tearDown()
    {
        $this->entityManager = null;
        $this->accountsRepository = null;
        $this->accountService = null;
    }

    public function testReturnNewAccountWithDefaultValues()
    {
        $account = new Accounts();
        $account->setFirstName("-");

        $result = $this->accountService->returnNewAccountWithDefaultValues();

        $this->assertEquals($account, $result);
    }

    public function testUpdateAccount()
    {
        $account = new Accounts();
        $account->setId(1);

        $this->accountsRepository
            ->expects($this->once())
            ->method("find")
            ->with($account->getId())
            ->willReturn($account);

        $account->setFirstName("ExampleNewValue");

        $this->entityManager
            ->expects($this->once())
            ->method("flush");

        $result = $this->accountService->updateAccount($account, 1);

        $this->assertEquals($account, $result);
    }
}
