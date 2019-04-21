<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\AccountType;
use App\Service\AccountService;
use App\Service\FormErrorsConverter;
use App\Service\UserService;
use Doctrine\ORM\NonUniqueResultException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class AccountController extends AbstractFOSRestController
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var FormErrorsConverter
     */
    private $converter;

    /**
     * @var AccountService
     */
    private $accountService;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * AccountController constructor.
     *
     * @param TranslatorInterface $translator
     * @param FormErrorsConverter $converter
     * @param AccountService $accountService
     * @param UserService $userService
     */
    public function __construct(
        TranslatorInterface $translator,
        FormErrorsConverter $converter,
        AccountService $accountService,
        UserService $userService
    ) {
        $this->translator = $translator;
        $this->converter = $converter;
        $this->accountService = $accountService;
        $this->userService = $userService;
    }

    /**
     * @Rest\Put("/accounts/{username}", name="update_account")
     *
     * @Security("is_granted('USER_EDIT', username)", statusCode=403)
     *
     * @param string $username
     * @param Request $request
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return Response
     */
    public function updateUserAccount(string $username, Request $request): Response
    {
        $form = $this->createForm(AccountType::class, null);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $data = $form->getData();

            $user = $this->userService->getUserByUsername($username);
            $updateResult = $this->accountService->updateAccount($data, $user->getAccount()->getId());

            if ($updateResult) {
                return $this->json([
                    'success' => $this->translator->trans('account_update'),
                ], Response::HTTP_OK);
            }

            return $this->json([
                'error' => $this->translator->trans('account_not_found'),
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'error' => $this->translator->trans('form_is_not_valid'),
        ], Response::HTTP_BAD_REQUEST);
    }
}
