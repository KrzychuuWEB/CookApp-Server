<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\UserDTO;
use App\Form\ChangePasswordType;
use App\Form\UserType;
use App\Service\FormErrorsConverter;
use App\Service\UserService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserController extends AbstractFOSRestController
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
     * @var UserService
     */
    private $userService;

    /**
     * UserController constructor.
     *
     * @param TranslatorInterface $translator
     * @param FormErrorsConverter $converter
     * @param UserService $userService
     */
    public function __construct(
        TranslatorInterface $translator,
        FormErrorsConverter $converter,
        UserService $userService
    ) {
        $this->translator = $translator;
        $this->converter = $converter;
        $this->userService = $userService;
    }

    /**
     * @Rest\Post("/users", name="user_create")
     *
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createUser(Request $request): Response
    {
        $form = $this->createForm(UserType::class, null);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $data = $form->getData();

            $this->userService->createUser($data);

            return $this->json([
                'success' => $this->translator->trans('create_user_account'),
            ], Response::HTTP_OK);
        }

        return $this->json([
            'error' => $this->translator->trans('form_is_not_valid'),
            'error_fields' => $this->converter->convertErrorsFromFrom($form),
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Get("/users/{username}", name="user_getOneByUsername")
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     *
     * @param string $username
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     *
     * @return Response
     */
    public function getOneUserByUsername(string $username): Response
    {
        $user = $this->userService->getUserByUsername($username);

        if ($user) {
            $userDTO = new UserDTO($user);

            $serializer = SerializerBuilder::create()->build();
            $data = $serializer->serialize(
                $userDTO,
                'json',
                SerializationContext::create()->enableMaxDepthChecks()
            );

            return $this->json([
                "user" => $data,
            ], Response::HTTP_OK);
        }

        return $this->json([
            "error" => $this->translator->trans('user_%username%_not_found', ['%username%' => $username]),
        ], Response::HTTP_NOT_FOUND);
    }

    /**
     * @Rest\Delete("/users/{username}", name="user_delete")
     *
     * @Security("is_granted('USER_DELETE')", statusCode="403")
     *
     * @param string $username
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function deleteUserByUsername(string $username): Response
    {
        $user = $this->userService->getUserByUsername($username);

        if ($user) {
            $this->userService->deleteUser($user);

            return $this->json([
                'success' => $this->translator->trans('user_delete'),
            ], Response::HTTP_OK);
        }

        return $this->json([
            'error' => $this->translator->trans('user_%username%_not_found', ['%username%' => $username]),
        ], Response::HTTP_NOT_FOUND);
    }

    /**
     * @Rest\Put("/users/{username}/passwords", name="user_changePassword")
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
    public function changeUserPassword(string $username, Request $request): Response
    {
        $form = $this->createForm(ChangePasswordType::class, null);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $data = $form->getData();

            $user = $this->userService->getUserByUsername($username);
            $changePasswordResult = $this->userService->changePassword($data, $user);

            if ($changePasswordResult) {
                return $this->json([
                    'success' => $this->translator->trans('password_update'),
                ], Response::HTTP_OK);
            }

            return $this->json([
                'error' => $this->translator->trans('old_password_not_correct'),
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->json([
            'error' => $this->translator->trans('form_is_not_valid'),
            'fields' => $this->converter->convertErrorsFromFrom($form),
        ], Response::HTTP_BAD_REQUEST);
    }
}
