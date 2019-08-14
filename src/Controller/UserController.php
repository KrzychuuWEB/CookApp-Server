<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\Type\UserCreateType;
use App\Service\CreateUser;
use App\Service\FormErrorConverter;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserController
 * @package App\Controller
 */
class UserController extends AbstractApiController
{
    private $createUser;

    public function __construct(TranslatorInterface $translator, FormErrorConverter $converter, CreateUser $createUser)
    {
        parent::__construct($translator, $converter);

        $this->createUser = $createUser;
    }

    /**
     * @Rest\Post("/users", name="user_create")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createUser(Request $request): Response
    {
        $form = $this->createForm(UserCreateType::class, null);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $data = $form->getData();
            $this->createUser->create($data);

            return $this->createRestResponse([
                'success' => $this->getTranslate('user_create'),
            ], 200);
        }

        return $this->createRestResponse([
            'error' => $this->getErrorsFromForm($form),
        ], Response::HTTP_BAD_REQUEST);
    }
}
