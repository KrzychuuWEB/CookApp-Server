<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\Type\UserCreateType;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 * @package App\Controller
 */
class UserController extends AbstractApiController
{
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
            return $this->createRestResponse([
                'data' => $this->getTranslate('user_create'),
            ], 200);
        }

        return $this->createRestResponse([
            'error' => $this->getErrorsFromForm($form),
        ]);
    }
}
