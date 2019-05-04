<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\PhotosCreateType;
use App\Service\FormErrorsConverter;
use App\Service\RecipePhotosService;
use App\Service\RecipeService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class RecipePhotosController extends AbstractFOSRestController
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
     * @var $recipePhotosService
     */
    private $recipePhotosService;

    /**
     * RecipeController constructor.
     *
     * @param TranslatorInterface $translator
     * @param FormErrorsConverter $converter
     * @param RecipeService $recipeService
     * @param RecipePhotosService $recipePhotosService
     */
    public function __construct(
        TranslatorInterface $translator,
        FormErrorsConverter $converter,
        RecipeService $recipeService,
        RecipePhotosService $recipePhotosService
    ) {
        $this->translator = $translator;
        $this->converter = $converter;
        $this->recipeService = $recipeService;
        $this->recipePhotosService = $recipePhotosService;
    }

    /**
     * @Rest\Post("/photos/recipes/{slug}", name="photos_create")
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
        $form = $this->createForm(PhotosCreateType::class, null);
        $arrToMerge = ['images' => $request->files->get('images')];
        $arrMerge = array_merge($request->request->all(), $arrToMerge);
        $form->submit($arrMerge);

        if ($form->isValid()) {
            $data = $form->getData();

            $recipe = $this->recipeService->getRecipeBySlug($slug);
            $this->recipePhotosService->createPhotos($data, $recipe);

            return $this->json([
                'success' => $this->translator->trans('recipephotos_created'),
            ], Response::HTTP_OK);
        }

        return $this->json([
            'error' => $this->translator->trans('form_is_not_valid'),
            'fields' => $this->converter->convertErrorsFromFrom($form),
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Delete("/photos/{id}/recipes/{slug}", name="photos_delete")
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
        $result = $this->recipePhotosService->deletePhoto($recipe->getId(), $id);

        if ($result) {
            return $this->json([
                'success' => $this->translator->trans('recipephotos_deleted'),
            ], Response::HTTP_OK);
        }

        return $this->json([
            'error' => $this->translator->trans('recipephotos_not_found')
        ], Response::HTTP_NOT_FOUND);
    }
}
