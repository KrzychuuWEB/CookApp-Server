<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\AccountsType;
use App\Form\UserType;
use App\Service\AccountService;
use App\Service\FormErrorsConverter;
use App\Service\UserService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractFOSRestController
{
    /**
     * @Rest\Post("/users", name="user_create")
     *
     * @param Request $request
     * @param UserService $userService
     * @param FormErrorsConverter $converter
     *
     * @return Response
     */
    public function register(
        Request $request,
        UserService $userService,
        FormErrorsConverter $converter
    ): Response {
        $form = $this->createForm(UserType::class, null);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $data = $form->getData();

            return $this->json([
                'success' => "User {$userService->createUser($data)} is registered",
            ], Response::HTTP_OK);
        }

        return $this->json([
            'error' => "Form is not valid",
            'error_fields' => $converter->convertErrorsFromFrom($form),
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Put("/users/{username}/accounts", name="user_updateUserAccount")
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
        $form = $this->createForm(AccountsType::class, null);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $data = $form->getData();

            $user = $userService->getUserByUsername($username);
            $loggedUser = $this->get("security.token_storage")->getToken()->getUser();

            if ($user) {
                if (strtolower($user->getUsername()) === strtolower($loggedUser->getUsername())) {
                    $updateResult = $accountService->updateAccount($data, $user->getAccount()->getId());

                    if ($updateResult) {
                        return $this->json([
                            'success' => "The $username user account has been updated",
                        ], Response::HTTP_OK);
                    }
                } else {
                    return $this->json([
                        'error' => "You don't have permission for update this account",
                    ], Response::HTTP_FORBIDDEN);
                }
            }

            return $this->json([
                'error' => "The $username user account was not found",
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->json([
            'error' => "Form is not valid",
            'error_fields' => $converter->convertErrorsFromFrom($form),
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Get("/users/{username}", name="user_getOneByUsername")
     *
     * @param string $username
     * @param UserService $userService
     *
     * @return Response
     */
    public function getOneUserByUsername(string $username, UserService $userService): Response
    {
        $user = $userService->getUserByUsername($username);

        $serializer = SerializerBuilder::create()->build();
        $data = $serializer->serialize(
            $user,
            'json',
            SerializationContext::create()->enableMaxDepthChecks()
        );

        if ($user) {
            return $this->json([
                "user" => $data,
            ], Response::HTTP_OK);
        }

        return $this->json([
            "error" => "User $username not found",
        ], Response::HTTP_BAD_REQUEST);
    }
}
