<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Ingredients;
use App\Entity\Recipe;
use App\Entity\RecipePhotos;
use App\Entity\Steps;
use App\Entity\User;
use App\Repository\IngredientsRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;

class RecipeService
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var RecipeRepository
     */
    private $recipeRepository;

    /**
     * @var IngredientsRepository
     */
    private $ingredientsRepository;

    /**
     * @var CreateCollectionService
     */
    private $createCollection;

    /**
     * RecipeService constructor.
     *
     * @param EntityManagerInterface $entity
     * @param RecipeRepository $recipeRepository
     * @param IngredientsRepository $ingredientsRepository
     * @param CreateCollectionService $createCollection
     */
    public function __construct(
        EntityManagerInterface $entity,
        RecipeRepository $recipeRepository,
        IngredientsRepository $ingredientsRepository,
        CreateCollectionService $createCollection
    ) {
        $this->entityManager = $entity;
        $this->recipeRepository = $recipeRepository;
        $this->ingredientsRepository = $ingredientsRepository;
        $this->createCollection = $createCollection;
    }

    /**
     * @param Recipe $formData
     * @param User $user
     *
     * @return string|null
     */
    public function createRecipe(Recipe $formData, User $user): ?string
    {
        $recipe = new Recipe();
        $recipe->setUser($user);
        $recipe->setName($formData->getName());
        $recipe->setDescription($formData->getDescription());
        $recipe->setLevel($formData->getLevel());
        $recipe->setTime($formData->getTime());
        $recipe->setSlug(
            $this->createUniqueSlugIfRecipeNameAlreadyExists($formData->getName())
        );

        foreach ($this->createCollection->create('ingredients', $formData) as $ingredient) {
            $recipe->addIngredient($ingredient);
            $this->entityManager->persist($ingredient);
        }

        foreach ($this->createCollection->create('steps', $formData) as $step) {
            $recipe->addStep($step);
            $this->entityManager->persist($step);
        }

        if ($formData->getImages()) {
            foreach ($this->createCollection->createPhotoCollectionAndUploadFile($formData) as $photo) {
                $recipe->addPhoto($photo);
                $this->entityManager->persist($photo);
            }
        }

        $this->entityManager->persist($recipe);
        $this->entityManager->flush();

        return $recipe->getSlug();
    }

    /**
     * @param string $slug
     *
     * @return Recipe|null
     */
    public function getRecipeBySlug(string $slug): ?Recipe
    {
        return $this->recipeRepository->findOneBy([
            'slug' => $slug,
            'isActive' => 1,
        ]);
    }

    /**
     * @param Recipe $recipe
     *
     * @return bool|null
     *
     * @throws \Exception
     */
    public function deleteRecipe(Recipe $recipe): ?bool
    {
        $recipe->setIsActive(false);
        $recipe->setDeletedAt();

        $this->entityManager->flush();

        return true;
    }

    /**
     * @param Recipe $formData
     * @param string $slug
     *
     * @return bool|null
     */
    public function changeInformation(Recipe $formData, string $slug): ?bool
    {
        $recipe = $this->getRecipeBySlug($slug);

        if (!$recipe instanceof Recipe) {
            return null;
        }

        $recipe->setName($formData->getName());
        $recipe->setDescription($formData->getDescription());
        $recipe->setLevel($formData->getLevel());
        $recipe->setTime($formData->getTime());

        $this->entityManager->flush();

        return true;
    }

    /**
     * @param Recipe $recipe
     *
     * @return array
     */
    private function createPhotosCollection(Recipe $recipe): array
    {
        $photos = [];

        foreach ($recipe->getImages() as $key => $value) {
            $photos[$key] = new RecipePhotos();
            $photos[$key]->setName(
                $this->fileUploaderService->upload($value)
            );
        }

        return $photos;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    private function createUniqueSlugIfRecipeNameAlreadyExists(string $string): string
    {
        $string = strtolower($string);
        $recipe = $this->getRecipeBySlug($string);

        if ($recipe) {
            $slug = str_replace(" ", "-", $string);

            return $slug . "-" . uniqid();
        }

        return str_replace(" ", "-", $string);
    }
}
