<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Ingredients;
use App\Entity\Recipe;
use App\Entity\RecipePhotos;
use App\Entity\Steps;

class CreateCollectionService
{
    /**
     * @var FileUploaderService
     */
    private $fileUploader;

    /**
     * CreateCollectionService constructor.
     *
     * @param FileUploaderService $fileUploader
     */
    public function __construct(FileUploaderService $fileUploader)
    {
        $this->fileUploader = $fileUploader;
    }

    /**
     * @param string $entity
     * @param Recipe $recipe
     *
     * @return array|bool
     */
    public function create(string $entity, Recipe $recipe): ?array
    {
        $arr = [];

        switch ($entity) {
            case 'ingredients':
                foreach ($recipe->getIngredients() as $key => $value) {
                    $arr[$key] = new Ingredients();
                    $arr[$key]->setName($value->getName());
                    $arr[$key]->setValue($value->getValue());
                    $arr[$key]->setUnit($value->getUnit());
                }
                break;
            case 'steps':
                foreach ($recipe->getSteps() as $key => $value) {
                    $arr[$key] = new Steps();
                    $arr[$key]->setName($value->getName());
                    $arr[$key]->setDescription($value->getDescription());
                    $arr[$key]->setStep($value->getStep());
                }
                break;
            default:
                return false;
                break;
        }

        return $arr;
    }

    public function createPhotoCollectionAndUploadFile(Recipe $recipe)
    {
        $photos = [];

        foreach ($recipe->getImages() as $key => $value) {
            $photos[$key] = new RecipePhotos();
            $photos[$key]->setName(
                $this->fileUploader->upload($value)
            );
        }

        return $photos;
    }
}
