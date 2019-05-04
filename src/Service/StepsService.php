<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Recipe;
use App\Entity\Steps;
use App\Repository\StepsRepository;
use Doctrine\ORM\EntityManagerInterface;

class StepsService
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var StepsRepository */
    private $stepsRepository;

    /**
     * @var CreateCollectionService
     */
    private $createCollection;

    /**
     * @param EntityManagerInterface $entityManager
     * @param StepsRepository $stepsRepository
     * @param CreateCollectionService $createCollection
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        StepsRepository $stepsRepository,
        CreateCollectionService $createCollection
    ) {
        $this->entityManager = $entityManager;
        $this->stepsRepository = $stepsRepository;
        $this->createCollection = $createCollection;
    }

    /**
     * @param Recipe $formData
     * @param Recipe $recipe
     *
     * @return bool
     */
    public function createSteps(Recipe $formData, Recipe $recipe): bool
    {
        $ingredients = new Steps();
        $ingredients->setRecipe($recipe);

        foreach ($this->createCollection->create("steps", $formData) as $step) {
            $recipe->addStep($step);
            $this->entityManager->persist($step);
        }

        $this->entityManager->flush();

        return true;
    }

    /**
     * @param Steps $formData
     * @param int $recipeId
     * @param int $stepId
     *
     * @return bool|null
     */
    public function changeStep(Steps $formData, int $recipeId, int $stepId): ?bool
    {
        $ingredient = $this->stepsRepository->findOneBy([
            'recipe' => $recipeId,
            'id' => $stepId
        ]);

        if (!$ingredient instanceof Steps) {
            return null;
        }

        $ingredient->setName($formData->getName());
        $ingredient->setDescription($formData->getDescription());
        $ingredient->setStep($formData->getStep());

        $this->entityManager->flush();

        return true;
    }

    /**
     * @param int $recipeId
     * @param int $stepId
     *
     * @return bool|null
     */
    public function deleteStep(int $recipeId, int $stepId): ?bool
    {
        $ingredient = $this->stepsRepository->findOneBy([
            'recipe' => $recipeId,
            'id' => $stepId
        ]);

        if (!$ingredient instanceof Steps) {
            return null;
        }

        $this->entityManager->remove($ingredient);
        $this->entityManager->flush();

        return true;
    }
}
