<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\UserType;
use App\Service\FormErrorsConverter;
use App\Service\UserService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
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

        if ($user) {
            return $this->json([
                "user" => $user,
            ], Response::HTTP_OK);
        }

        return $this->json([
            "error" => "User $username not found",
        ], Response::HTTP_BAD_REQUEST);
    }
}
