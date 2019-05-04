<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\RecipeDTO;
use App\Form\RecipeChangeInformationType;
use App\Form\RecipeType;
use App\Service\FormErrorsConverter;
use App\Service\IngredientsService;
use App\Service\RecipeService;
use App\Service\UserService;
use Doctrine\ORM\NonUniqueResultException;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class RecipeController extends AbstractFOSRestController
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
     * @var UserService
     */
    private $userService;

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
     * @param UserService $userService
     * @param IngredientsService $ingredientsService
     */
    public function __construct(
        TranslatorInterface $translator,
        FormErrorsConverter $converter,
        RecipeService $recipeService,
        UserService $userService,
        IngredientsService $ingredientsService
    ) {
        $this->translator = $translator;
        $this->converter = $converter;
        $this->recipeService = $recipeService;
        $this->userService = $userService;
        $this->ingredientsService = $ingredientsService;
    }

    /**
     * @Rest\Post("/recipes", name="recipe_create")
     *
     * @param Request $request
     *
     * @throws NonUniqueResultException
     *
     * @return Response
     */
    public function create(Request $request): Response
    {
        $form = $this->createForm(RecipeType::class, null);

        $ingredients = [];
        $steps = [];

        foreach ($request->request->get('ingredients') as $ingredient) {
            $ingredients[] = json_decode($ingredient, true);
        }

        foreach ($request->request->get('steps') as $step) {
            $steps[] = json_decode($step, true);
        }

        $arrayToMerge = [
            'images' => $request->files->get('images'),
            'ingredients' => $ingredients,
            'steps' => $steps,
        ];

        $arrayMerge = array_merge($request->request->all(), $arrayToMerge);
        $form->submit($arrayMerge);

        if ($form->isValid()) {
            $data = $form->getData();
            $user = $this->userService->getUserByUsername(
                $this->get('security.token_storage')->getToken()->getUser()->getUsername()
            );

            return $this->json([
                'success' => $this->translator->trans('recipe_created'),
                'slug' => $this->recipeService->createRecipe($data, $user)
            ], Response::HTTP_CREATED);
        }

        return $this->json([
            'error' => $this->translator->trans('form_is_not_valid'),
            'fields' => $this->converter->convertErrorsFromFrom($form),
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Put("/recipes/information/{slug}", name="recipe_change_information")
     *
     * @param string $slug
     * @param Request $request
     *
     * @return Response
     */
    public function changeRecipeInformation(string $slug, Request $request): Response
    {
        $form = $this->createForm(RecipeChangeInformationType::class, null);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $data = $form->getData();

            $result = $this->recipeService->changeInformation($data, $slug);

            if ($result) {
                return $this->json([
                    'success' => $this->translator->trans('recipe_updated'),
                ], Response::HTTP_OK);
            }

            return $this->json([
                'error' => $this->translator->trans('recipe_not_found'),
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'error' => $this->translator->trans('form_is_not_valid'),
            'fields' => $this->converter->convertErrorsFromFrom($form),
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Get("/recipes/{slug}", name="recipe_read")
     *
     * @param string $slug
     *
     * @return Response
     */
    public function read(string $slug): Response
    {
        $recipe = $this->recipeService->getRecipeBySlug($slug);

        if ($recipe) {
            $recipeDTO = new RecipeDTO($recipe);

            $serializer = SerializerBuilder::create()->build();
            $data = $serializer->serialize(
                $recipeDTO,
                'json',
                SerializationContext::create()->setGroups(['read_recipe'])
            );

            return $this->json([
                'recipe' => $data
            ], Response::HTTP_OK);
        }

        return $this->json([
            'error' => $this->translator->trans('recipe_not_found'),
        ], Response::HTTP_NOT_FOUND);
    }

    /**
     * @Rest\Delete("/recipes/{slug}", name="recipe_delete")
     *
     * @param string $slug
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function delete(string $slug): Response
    {
        $recipe = $this->recipeService->getRecipeBySlug($slug);

        if ($recipe) {
            $this->recipeService->deleteRecipe($recipe);

            return $this->json([
                'success' => $this->translator->trans('recipe_deleted'),
            ], Response::HTTP_OK);
        }

        return $this->json([
            'error' => $this->translator->trans('recipe_not_found'),
        ], Response::HTTP_NOT_FOUND);
    }
}
