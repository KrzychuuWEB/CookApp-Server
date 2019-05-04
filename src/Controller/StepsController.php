<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\StepsCreateType;
use App\Form\StepsType;
use App\Service\FormErrorsConverter;
use App\Service\IngredientsService;
use App\Service\RecipeService;
use App\Service\StepsService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class StepsController extends AbstractFOSRestController
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
     * @var RecipeService
     */
    private $recipeService;

    /**
     * @var StepsService
     */
    private $stepsService;

    /**
     * RecipeController constructor.
     *
     * @param TranslatorInterface $translator
     * @param FormErrorsConverter $converter
     * @param RecipeService $recipeService
     * @param StepsService $stepsService
     */
    public function __construct(
        TranslatorInterface $translator,
        FormErrorsConverter $converter,
        RecipeService $recipeService,
        StepsService $stepsService
    ) {
        $this->translator = $translator;
        $this->converter = $converter;
        $this->recipeService = $recipeService;
        $this->stepsService = $stepsService;
    }

    /**
     * @Rest\Post("/steps/recipes/{slug}", name="steps_create")
     *
     * @Security("is_granted('RECIPE_EDIT', slug)", statusCode=403)
     *
     * @param string $slug
     * @param Request $request
     *
     * @return Response
     */
    public function create(string $slug, Request $request): Response
    {
        $form = $this->createForm(StepsCreateType::class, null);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $data = $form->getData();

            $recipe = $this->recipeService->getRecipeBySlug($slug);
            $this->stepsService->createSteps($data, $recipe);

            return $this->json([
                'success' => $this->translator->trans('steps_created'),
            ], Response::HTTP_OK);
        }

        return $this->json([
            'error' => $this->translator->trans('form_is_not_valid'),
            'fields' => $this->converter->convertErrorsFromFrom($form),
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Put("/steps/{id}/recipes/{slug}", name="steps_update")
     *
     * @Security("is_granted('RECIPE_EDIT', slug)", statusCode=403)
     *
     * @param int $id
     * @param string $slug
     * @param Request $request
     *
     * @return Response
     */
    public function update(int $id, string $slug, Request $request): Response
    {
        $form = $this->createForm(StepsType::class, null);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $data = $form->getData();

            $recipeId = $this->recipeService->getRecipeBySlug($slug)->getId();
            $result = $this->stepsService->changeStep($data, $recipeId, $id);

            if ($result) {
                return $this->json([
                    'success' => $this->translator->trans('steps_updated'),
                ], Response::HTTP_OK);
            }

            return $this->json([
                'error' => $this->translator->trans('steps_not_found'),
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'error' => $this->translator->trans('form_is_not_valid'),
            'fields' => $this->converter->convertErrorsFromFrom($form),
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Delete("/steps/{id}/recipes/{slug}", name="steps_delete")
     *
     * @Security("is_granted('RECIPE_EDIT', slug)", statusCode=403)
     *
     * @param string $slug
     * @param int $id
     *
     * @return Response
     */
    public function delete(int $id, string $slug): Response
    {
        $recipe = $this->recipeService->getRecipeBySlug($slug);
        $result = $this->stepsService->deleteStep($recipe->getId(), $id);

        if ($result) {
            return $this->json([
                'success' => $this->translator->trans('steps_deleted'),
            ], Response::HTTP_OK);
        }

        return $this->json([
            'error' => $this->translator->trans('steps_not_found')
        ], Response::HTTP_NOT_FOUND);
    }
}
