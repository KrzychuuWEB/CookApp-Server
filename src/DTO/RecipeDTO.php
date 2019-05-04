<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Recipe;
use JMS\Serializer\Annotation as Serializer;

class RecipeDTO
{
    /**
     * @var string
     * @Serializer\Groups({"read_recipe"})
     */
    private $author;

    /**
     * @var string|null
     * @Serializer\Groups({"read_recipe"})
     */
    private $name;

    /**
     * @var string|null
     * @Serializer\Groups({"read_recipe"})
     */
    private $description;

    /**
     * @var int|null
     * @Serializer\Groups({"read_recipe"})
     */
    private $level;

    /**
     * @var int|null
     * @Serializer\Groups({"read_recipe"})
     */
    private $time;

    /**
     * @var \DateTime|\DateTimeInterface|null
     * @Serializer\Groups({"read_recipe"})
     */
    private $created_at;

    /**
     * @var \DateTime|\DateTimeInterface|null
     * @Serializer\Groups({"read_recipe"})
     */
    private $edited_at;

    /**
     * @var string|null
     * @Serializer\Groups({"read_recipe"})
     */
    private $slug;

    /**
     * @var \App\Entity\Ingredients[]|\Doctrine\Common\Collections\Collection
     * @Serializer\Groups({"read_recipe"})
     */
    private $ingredients;

    /**
     * @var \App\Entity\Steps[]|\Doctrine\Common\Collections\Collection
     * @Serializer\Groups({"read_recipe"})
     */
    private $steps;

    /**
     * @var \App\Entity\RecipePhotos[]|\Doctrine\Common\Collections\Collection
     * @Serializer\Groups({"read_recipe"})
     */
    private $photos;

    /**
     * RecipeDTO constructor
     *
     * @param Recipe $recipe
     */
    public function __construct(Recipe $recipe)
    {
        $this->author = $recipe->getUser()->getUsername();

        $this->name = $recipe->getName();
        $this->description = $recipe->getDescription();
        $this->level = $recipe->getLevel();
        $this->time = $recipe->getTime();
        $this->created_at = $recipe->getCreatedAt();
        $this->edited_at = $recipe->getEditedAt();
        $this->slug = $recipe->getSlug();

        $this->ingredients = $recipe->getIngredients();

        $this->steps = $recipe->getSteps();

        $this->photos = $recipe->getPhotos();
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return int|null
     */
    public function getLevel(): ?int
    {
        return $this->level;
    }

    /**
     * @return int|null
     */
    public function getTime(): ?int
    {
        return $this->time;
    }

    /**
     * @return \DateTime|\DateTimeInterface|null
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @return \DateTime|\DateTimeInterface|null
     */
    public function getEditedAt()
    {
        return $this->edited_at;
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @return \App\Entity\Ingredients[]|\Doctrine\Common\Collections\Collection
     */
    public function getIngredients()
    {
        return $this->ingredients;
    }

    /**
     * @return \App\Entity\Steps[]|\Doctrine\Common\Collections\Collection
     */
    public function getSteps()
    {
        return $this->steps;
    }

    /**
     * @return \App\Entity\RecipePhotos[]|\Doctrine\Common\Collections\Collection
     */
    public function getPhotos()
    {
        return $this->photos;
    }
}
