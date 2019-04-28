<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\PermissionDeleteType;
use App\Form\PermissionType;
use App\Form\PermissionUpdateType;
use App\Form\UserPermissionsType;
use App\Service\FormErrorsConverter;
use App\Service\PermissionService;
use App\Service\StringConverterService;
use App\Service\UserService;
use Doctrine\ORM\NonUniqueResultException;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Security("is_granted('ROLE_MANAGEMENT')", statusCode=403)
 */
class PermissionController extends AbstractFOSRestController
{
    /**
     * @var PermissionService
     */
    private $permissionService;

    /**
     * @var FormErrorsConverter
     */
    private $formErrorsConverter;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var StringConverterService
     */
    private $stringConverter;

    /**
     * PermissionController constructor.
     *
     * @param PermissionService $permissionService
     * @param FormErrorsConverter $converter
     * @param TranslatorInterface $translator
     * @param UserService $userService
     * @param StringConverterService $stringConverter
     */
    public function __construct(
        PermissionService $permissionService,
        FormErrorsConverter $converter,
        TranslatorInterface $translator,
        UserService $userService,
        StringConverterService $stringConverter
    ) {
        $this->permissionService = $permissionService;
        $this->formErrorsConverter = $converter;
        $this->translator = $translator;
        $this->userService = $userService;
        $this->stringConverter = $stringConverter;
    }

    /**
     * @Rest\Post("/permissions", name="permission_create")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request): Response
    {
        $form = $this->createForm(PermissionType::class, null);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $data = $form->getData();

            $this->permissionService->createPermission($data);

            return $this->json([
                'success' => $this->translator->trans('permission_created'),
            ], Response::HTTP_CREATED);
        }

        return $this->json([
            'error' => $this->translator->trans('form_is_not_valid'),
            'fields' => $this->formErrorsConverter->convertErrorsFromFrom($form),
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Delete("/permissions/{permission}", name="permission_delete")
     *
     * @param string $permission
     *
     * @return Response
     */
    public function delete(string $permission): Response
    {
        $result = $this->permissionService->deletePermission($permission);

        if ($result) {
            return $this->json([
                'success' => $this->translator->trans('permission_delete'),
            ], Response::HTTP_OK);
        } elseif ($result === null) {
            return $this->json([
                'error' => $this->translator->trans('permission_not_found'),
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'success' => $this->translator->trans('permission_delete_has_users'),
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Put("/permissions/{permission}", name="permission_update")
     *
     * @param string $permission
     * @param Request $request
     *
     * @return Response
     */
    public function update(string $permission, Request $request): Response
    {
        $form = $this->createForm(PermissionUpdateType::class, null);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $data = $form->getData();

            $permission = $this->stringConverter->setUppercase($permission);
            $permission = $this->stringConverter->addPrefix($permission, "ROLE_");

            $result = $this->permissionService->updatePermission($data, $permission);

            if ($result) {
                return $this->json([
                    'success' => $this->translator->trans('permission_update'),
                ], Response::HTTP_OK);
            }

            return $this->json([
                'error' => $this->translator->trans('permission_not_found'),
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'error' => $this->translator->trans('form_is_not_valid'),
            'fields' => $this->formErrorsConverter->convertErrorsFromFrom($form),
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Post("/permissions/users/{username}", name="permission_user_add")
     *
     * @param string $username
     * @param Request $request
     *
     * @return Response
     *
     * @throws NonUniqueResultException
     */
    public function addPermission(
        string $username,
        Request $request
    ): Response {
        $form = $this->createForm(UserPermissionsType::class, null);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $data = $form->getData();

            $user = $this->userService->getUserByUsername($username);

            if ($user) {
                $result = $this->permissionService->addPermissionForUser($data, $user);

                if ($result) {
                    return $this->json([
                        'success' => $this->translator->trans('user_add_permission'),
                    ], Response::HTTP_OK);
                } elseif ($result === null) {
                    return $this->json([
                        'error' => $this->translator->trans('permission_not_found'),
                    ], Response::HTTP_NOT_FOUND);
                }

                return $this->json([
                    'error' => $this->translator->trans('user_has_permission'),
                ], Response::HTTP_BAD_REQUEST);

            }

            return $this->json([
                'error' => $this->translator->trans('user_%username%_not_found', ['%username%' => $username]),
            ], Response::HTTP_FOUND);
        }

        return $this->json([
            'error' => $this->translator->trans('form_is_not_valid'),
            'fields' => $this->formErrorsConverter->convertErrorsFromFrom($form),
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Delete("/permissions/users/{username}", name="permission_user_delete")
     *
     * @param string $username
     * @param Request $request
     *
     * @return Response
     *
     * @throws NonUniqueResultException
     */
    public function deletePermission(
        string $username,
        Request $request
    ): Response {
        $form = $this->createForm(UserPermissionsType::class, null);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $data = $form->getData();

            $user = $this->userService->getUserByUsername($username);

            if ($user) {
                $result = $this->permissionService->deletePermissionForUser($data, $user);

                if ($result) {
                    return $this->json([
                        'success' => $this->translator->trans('user_delete_permission'),
                    ], Response::HTTP_OK);
                } elseif ($result === null) {
                    return $this->json([
                        'error' => $this->translator->trans('permission_not_found'),
                    ], Response::HTTP_NOT_FOUND);
                }

                return $this->json([
                    'error' => $this->translator->trans('user_not_has_permission'),
                ], Response::HTTP_BAD_REQUEST);

            }

            return $this->json([
                'error' => $this->translator->trans('user_%username%_not_found', ['%username%' => $username]),
            ], Response::HTTP_FOUND);
        }

        return $this->json([
            'error' => $this->translator->trans('form_is_not_valid'),
            'fields' => $this->formErrorsConverter->convertErrorsFromFrom($form),
        ], Response::HTTP_BAD_REQUEST);
    }
}
