<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\Type\UserCreateType;
use App\Service\CreateUserService;
use App\Service\FormErrorConverter;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class UserController
 * @package App\Controller
 */
class UserController extends AbstractApiController
{
    private $createUser;

    public function __construct(TranslatorInterface $translator, FormErrorConverter $converter, CreateUserService $createUser)
    {
        parent::__construct($translator, $converter);

        $this->createUser = $createUser;
    }

    /**
     * @Rest\Post("/users", name="create_user")
     *
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createUser(Request $request): Response
    {
        $form = $this->createForm(UserCreateType::class, null);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $data = $form->getData();
            $this->createUser->createUser($data);

            return $this->createRestResponse(true, null, $this->getTranslate('user_create'), Response::HTTP_OK);
        }

        return $this->createRestResponse(false, $this->getErrorsFromForm($form), null, Response::HTTP_BAD_REQUEST);
    }
}
