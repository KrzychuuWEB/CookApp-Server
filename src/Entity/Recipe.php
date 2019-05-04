<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RecipeRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Recipe
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="recipes")
     * @Serializer\Groups({"read_recipe"})
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=120)
     * @Serializer\Groups({"read_recipe"})
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Serializer\Groups({"read_recipe"})
     */
    private $description;

    /**
     * @ORM\Column(type="smallint")
     * @Serializer\Groups({"read_recipe"})
     */
    private $level;

    /**
     * @ORM\Column(type="smallint")
     * @Serializer\Groups({"read_recipe"})
     */
    private $time;

    /**
     * @ORM\Column(type="datetime")
     * @Serializer\Groups({"read_recipe"})
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Serializer\Groups({"read_recipe"})
     */
    private $edited_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deleted_at;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Ingredients", mappedBy="recipe", cascade={"persist"})
     * @Serializer\Groups({"read_recipe"})
     */
    private $ingredients;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Steps", mappedBy="recipe", cascade={"persist"})
     * @Serializer\Groups({"read_recipe"}))
     */
    private $steps;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RecipePhotos", mappedBy="recipe", orphanRemoval=true)
     * @Serializer\Groups({"read_recipe"})
     */
    private $photos;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Groups({"read_recipe"})
     */
    private $slug;

    /**
     * Field used for get images from request
     *
     * @var array
     */
    private $images;

    public function __construct()
    {
        $this->isActive = true;
        $this->ingredients = new ArrayCollection();
        $this->steps = new ArrayCollection();
        $this->photos = new ArrayCollection();
    }

    /**
     * @return array
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param array $images
     *
     * @return Recipe
     */
    public function setImages(array $images): self
    {
        $this->images = $images;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return Recipe
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     *
     * @return Recipe
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Recipe
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     * @return Recipe
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLevel(): ?int
    {
        return $this->level;
    }

    /**
     * @param int $level
     *
     * @return Recipe
     */
    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTime(): ?int
    {
        return $this->time;
    }

    public function setTime(int $time): self
    {
        $this->time = $time;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->created_at;
    }

    /**
     * @ORM\PrePersist
     *
     * @return Recipe
     *
     * @throws \Exception
     */
    public function setCreatedAt(): self
    {
        $this->created_at = new \DateTime();

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getEditedAt(): ?\DateTime
    {
        return $this->edited_at;
    }

    /**
     * @ORM\PreUpdate
     *
     * @return Recipe
     *
     * @throws \Exception
     */
    public function setEditedAt(): self
    {
        $this->edited_at = new \DateTime();

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDeletedAt(): ?\DateTime
    {
        return $this->deleted_at;
    }

    /**
     * @return Recipe
     *
     * @throws \Exception
     */
    public function setDeletedAt(): self
    {
        $this->deleted_at = new \DateTime();

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     *
     * @return Recipe
     */
    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection|Ingredients[]
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    public function addIngredient(Ingredients $ingredient): self
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients[] = $ingredient;
            $ingredient->setRecipe($this);
        }

        return $this;
    }

    public function removeIngredient(Ingredients $ingredient): self
    {
        if ($this->ingredients->contains($ingredient)) {
            $this->ingredients->removeElement($ingredient);
            // set the owning side to null (unless already changed)
            if ($ingredient->getRecipe() === $this) {
                $ingredient->setRecipe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Steps[]
     */
    public function getSteps(): Collection
    {
        return $this->steps;
    }

    public function addStep(Steps $step): self
    {
        if (!$this->steps->contains($step)) {
            $this->steps[] = $step;
            $step->setRecipe($this);
        }

        return $this;
    }

    public function removeStep(Steps $step): self
    {
        if ($this->steps->contains($step)) {
            $this->steps->removeElement($step);
            // set the owning side to null (unless already changed)
            if ($step->getRecipe() === $this) {
                $step->setRecipe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|RecipePhotos[]
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(RecipePhotos $photo): self
    {
        if (!$this->photos->contains($photo)) {
            $this->photos[] = $photo;
            $photo->setRecipe($this);
        }

        return $this;
    }

    public function removePhoto(RecipePhotos $photo): self
    {
        if ($this->photos->contains($photo)) {
            $this->photos->removeElement($photo);
            // set the owning side to null (unless already changed)
            if ($photo->getRecipe() === $this) {
                $photo->setRecipe(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
