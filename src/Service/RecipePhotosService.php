<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Recipe;
use App\Entity\RecipePhotos;
use App\Repository\RecipePhotosRepository;
use Doctrine\ORM\EntityManagerInterface;

class RecipePhotosService
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var RecipePhotosRepository */
    private $recipePhotosRepository;

    /**
     * @var CreateCollectionService
     */
    private $createCollection;

    /**
     * @param EntityManagerInterface $entityManager
     * @param RecipePhotosRepository $recipePhotosRepository
     * @param CreateCollectionService $createCollection
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        RecipePhotosRepository $recipePhotosRepository,
        CreateCollectionService $createCollection
    ) {
        $this->entityManager = $entityManager;
        $this->recipePhotosRepository = $recipePhotosRepository;
        $this->createCollection = $createCollection;
    }

    /**
     * @param Recipe $formData
     * @param Recipe $recipe
     *
     * @return bool
     */
    public function createPhotos(Recipe $formData, Recipe $recipe): bool
    {
        $recipePhotos = new RecipePhotos();
        $recipePhotos->setRecipe($recipe);

        foreach ($this->createCollection->createPhotoCollectionAndUploadFile($formData) as $image) {
            $recipe->addPhoto($image);
            $this->entityManager->persist($image);
        }

        $this->entityManager->flush();

        return true;
    }

    /**
     * @param int $recipeId
     * @param int $imageId
     *
     * @return bool|null
     */
    public function deletePhoto(int $recipeId, int $imageId): ?bool
    {
        $recipePhoto = $this->recipePhotosRepository->findOneBy([
            'recipe' => $recipeId,
            'id' => $imageId
        ]);

        if (!$recipePhoto instanceof RecipePhotos) {
            return null;
        }

        $this->entityManager->remove($recipePhoto);
        $this->entityManager->flush();

        return true;
    }
}
