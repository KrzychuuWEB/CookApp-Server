<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\IngredientsCreateType;
use App\Form\IngredientsType;
use App\Service\FormErrorsConverter;
use App\Service\IngredientsService;
use App\Service\RecipeService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class IngredientsController extends AbstractFOSRestController
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
     * @var IngredientsService
     */
    private $ingredientsService;

    /**
     * RecipeController constructor.
     *
     * @param TranslatorInterface $translator
     * @param FormErrorsConverter $converter
     * @param RecipeService $recipeService
     * @param IngredientsService $ingredientsService
     */
    public function __construct(
        TranslatorInterface $translator,
        FormErrorsConverter $converter,
        RecipeService $recipeService,
        IngredientsService $ingredientsService
    ) {
        $this->translator = $translator;
        $this->converter = $converter;
        $this->recipeService = $recipeService;
        $this->ingredientsService = $ingredientsService;
    }

    /**
     * @Rest\Post("/ingredients/recipes/{slug}", name="ingredients_create")
     *
     * @Security("is_granted('RECIPE_EDIT', slug)", statusCode=403)
     *
     * @param string $slug
     * @param Request $request
     *
     * @return Response
     */
    public function createIngredient(string $slug, Request $request): Response
    {
        $form = $this->createForm(IngredientsCreateType::class, null);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $data = $form->getData();

            $recipe = $this->recipeService->getRecipeBySlug($slug);
            $this->ingredientsService->createIngredients($data, $recipe);

            return $this->json([
                'success' => $this->translator->trans('ingredients_created'),
            ], Response::HTTP_OK);
        }

        return $this->json([
            'error' => $this->translator->trans('form_is_not_valid'),
            'fields' => $this->converter->convertErrorsFromFrom($form),
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Put("/ingredients/{id}/recipes/{slug}", name="ingredients_update")
     *
     * @Security("is_granted('RECIPE_EDIT', slug)", statusCode=403)
     *
     * @param int $id
     * @param string $slug
     * @param Request $request
     *
     * @return Response
     */
    public function changeRecipeIngredient(int $id, string $slug, Request $request): Response
    {
        $form = $this->createForm(IngredientsType::class, null);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $data = $form->getData();

            $recipeId = $this->recipeService->getRecipeBySlug($slug)->getId();
            $result = $this->ingredientsService->changeIngredient($data, $recipeId, $id);

            if ($result) {
                return $this->json([
                    'success' => $this->translator->trans('ingredients_updated'),
                ], Response::HTTP_OK);
            }

            return $this->json([
                'error' => $this->translator->trans('ingredients_not_found'),
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'error' => $this->translator->trans('form_is_not_valid'),
            'fields' => $this->converter->convertErrorsFromFrom($form),
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Delete("/ingredients/{id}/recipes/{slug}", name="ingredients_delete")
     *
     * @Security("is_granted('RECIPE_EDIT', slug)", statusCode=403)
     *
     * @param string $slug
     * @param int $id
     *
     * @return Response
     */
    public function deleteIngredient(int $id, string $slug): Response
    {
        $recipe = $this->recipeService->getRecipeBySlug($slug);
        $result = $this->ingredientsService->deleteIngredient($recipe->getId(), $id);

        if ($result) {
            return $this->json([
                'success' => $this->translator->trans('ingredients_deleted'),
            ], Response::HTTP_OK);
        }

        return $this->json([
            'error' => $this->translator->trans('ingredients_not_found')
        ], Response::HTTP_NOT_FOUND);
    }
}
