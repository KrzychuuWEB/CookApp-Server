<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\AccountType;
use App\Service\AccountService;
use App\Service\FormErrorsConverter;
use App\Service\UserService;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends AbstractFOSRestController
{
    /**
     * @Rest\Put("/accounts/{username}", name="update_account")
     *
     * @Security("is_granted('ACCOUNT_EDIT', username)", statusCode=403)
     *
     * @param string $username
     * @param Request $request
     * @param AccountService $accountService
     * @param UserService $userService
     * @param FormErrorsConverter $converter
     *
     * @return Response
     */
    public function updateUserAccount(
        string $username,
        Request $request,
        AccountService $accountService,
        UserService $userService,
        FormErrorsConverter $converter
    ): Response {
        $form = $this->createForm(AccountType::class, null);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $data = $form->getData();

            $user = $userService->getUserByUsername($username);
            $accountService->updateAccount($data, $user->getAccount()->getId());

            return $this->json([
                'success' => "The $username user account has been updated",
            ], Response::HTTP_OK);
        }

        return $this->json([
            'error' => "Form is not valid",
            'error_fields' => $converter->convertErrorsFromFrom($form),
        ], Response::HTTP_BAD_REQUEST);
    }
}
