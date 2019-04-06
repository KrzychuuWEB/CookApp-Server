<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Accounts;
use App\Repository\AccountsRepository;
use Doctrine\ORM\EntityManagerInterface;

class AccountService
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var AccountsRepository */
    private $accountsRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param AccountsRepository $accountsRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        AccountsRepository $accountsRepository
    ) {
        $this->entityManager = $entityManager;
        $this->accountsRepository = $accountsRepository;
    }

    /**
     * @return Accounts
     */
    public function returnNewAccountWithDefaultValues(): Accounts
    {
        $account = new Accounts();
        $account->setFirstName("-");
        $account->setLastName("-");
        $account->setAge(0);
        $account->setHobby("-");
        $account->setCountry("-");
        $account->setCity("-");
        $account->setAboutMe("-");

        return $account;
    }

    /**
     * @param Accounts $data
     * @param int $accountId
     *
     * @return Accounts|boolean
     *
     * TODO Add return type declaration
     */
    public function updateAccount(Accounts $data, int $accountId)
    {
        $account = $this->accountsRepository->find($accountId);

        if (!$account instanceof Accounts) {
            return false;
        }

        $account->setFirstName($data->getFirstName());
        $account->setLastName($data->getLastName());
        $account->setAge($data->getAge());
        $account->setHobby($data->getHobby());
        $account->setCountry($data->getCountry());
        $account->setCity($data->getCity());
        $account->setAboutMe($data->getAboutMe());

        $this->entityManager->flush();

        return $account;
    }
}
