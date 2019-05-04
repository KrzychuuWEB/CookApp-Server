<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Ingredients;
use App\Entity\Recipe;
use App\Repository\IngredientsRepository;
use Doctrine\ORM\EntityManagerInterface;

class IngredientsService
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var IngredientsRepository */
    private $ingredientsRepository;

    /**
     * @var CreateCollectionService
     */
    private $createCollection;

    /**
     * @param EntityManagerInterface $entityManager
     * @param IngredientsRepository $ingredientsRepository
     * @param CreateCollectionService $createCollection
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        IngredientsRepository $ingredientsRepository,
        CreateCollectionService $createCollection
    ) {
        $this->entityManager = $entityManager;
        $this->ingredientsRepository = $ingredientsRepository;
        $this->createCollection = $createCollection;
    }

    /**
     * @param Recipe $formData
     * @param Recipe $recipe
     *
     * @return bool
     */
    public function createIngredients(Recipe $formData, Recipe $recipe): bool
    {
        $ingredients = new Ingredients();
        $ingredients->setRecipe($recipe);

        foreach ($this->createCollection->create("ingredients", $formData) as $ingredient) {
            $recipe->addIngredient($ingredient);
            $this->entityManager->persist($ingredient);
        }

        $this->entityManager->flush();

        return true;
    }

    /**
     * @param Ingredients $formData
     * @param int $recipeId
     * @param int $ingredientId
     *
     * @return bool|null
     */
    public function changeIngredient(Ingredients $formData, int $recipeId, int $ingredientId): ?bool
    {
        $ingredient = $this->ingredientsRepository->findOneBy([
            'recipe' => $recipeId,
            'id' => $ingredientId
        ]);

        if (!$ingredient instanceof Ingredients) {
            return null;
        }

        $ingredient->setName($formData->getName());
        $ingredient->setValue($formData->getValue());
        $ingredient->setUnit($formData->getUnit());

        $this->entityManager->flush();

        return true;
    }

    /**
     * @param int $recipeId
     * @param int $ingredientId
     *
     * @return bool|null
     */
    public function deleteIngredient(int $recipeId, int $ingredientId): ?bool
    {
        $ingredient = $this->ingredientsRepository->findOneBy([
            'recipe' => $recipeId,
            'id' => $ingredientId
        ]);

        if (!$ingredient instanceof Ingredients) {
            return null;
        }

        $this->entityManager->remove($ingredient);
        $this->entityManager->flush();

        return true;
    }
}
