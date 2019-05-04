<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Ingredients;
use App\Entity\Recipe;
use App\Entity\Steps;
use App\Entity\User;
use App\Repository\IngredientsRepository;
use App\Repository\RecipeRepository;
use App\Service\CreateCollectionService;
use App\Service\FileUploaderService;
use App\Service\RecipeService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class RecipeServiceTest extends TestCase
{
    /**
     * @var EntityManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManager;

    /**
     * @var RecipeRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $recipeRepository;

    /**
     * @var RecipeService
     */
    private $recipeService;

    /**
     * @var RecipeService|\PHPUnit_Framework_MockObject_MockObject
     */
    private $recipeServiceMock;

    /**
     * @var IngredientsRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ingredientsRepository;

    /**
     * @var CreateCollectionService|\PHPUnit_Framework_MockObject_MockObject
     */
    private $createCollection;

    protected function setUp()
    {
        $this->entityManager = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->recipeRepository = $this->getMockBuilder(RecipeRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->recipeServiceMock = $this->getMockBuilder(RecipeService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->ingredientsRepository = $this->getMockBuilder(IngredientsRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->createCollection = $this->getMockBuilder(CreateCollectionService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->recipeService = new RecipeService(
            $this->entityManager,
            $this->recipeRepository,
            $this->ingredientsRepository,
            $this->createCollection
        );
    }

    protected function tearDown()
    {
        $this->entityManager = null;
        $this->recipeRepository = null;
        $this->recipeService = null;
        $this->createCollection = null;
        $this->ingredientsRepository = null;
    }

    public function testCreateRecipe()
    {
        $user = new User();
        $user->setUsername("Example");

        $recipe = new Recipe();
        $recipe->setUser($user);
        $recipe->setName('Example name');
        $recipe->setDescription("Example description");
        $recipe->setLevel(1); // Easy
        $recipe->setTime(35); // 35 minutes
        $recipe->setSlug('example-name');

        $ingredients[0] = new Ingredients();
        $ingredients[0]->setName("Example name");
        $ingredients[0]->setUnit("Example unit");
        $ingredients[0]->setValue("Example value");

        $steps[0] = new Steps();
        $steps[0]->setName("Example name");
        $steps[0]->setDescription("Example description");
        $steps[0]->setStep(1);

        $recipe->addIngredient($ingredients[0]);
        $recipe->addStep($steps[0]);

        $this->createCollection
            ->expects($this->at(0))
            ->method('create')
            ->with('ingredients', $recipe)
            ->willReturn($ingredients);
        $this->createCollection
            ->expects($this->at(1))
            ->method('create')
            ->with('steps', $recipe)
            ->willReturn($steps);

        $this->entityManager
            ->expects($this->any())
            ->method('persist')
            ->willReturn($ingredients);
        $this->entityManager
            ->expects($this->any())
            ->method('persist')
            ->willReturn($steps);
        $this->entityManager
            ->expects($this->any())
            ->method('persist')
            ->willReturn($recipe);
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $result = $this->recipeService->createRecipe($recipe, $user);

        $this->assertEquals($result, $recipe->getSlug());
    }

    public function testGetRecipeBySlug()
    {
        $recipe = new Recipe();
        $recipe->setName("Example name");
        $recipe->setIsActive(true);
        $recipe->setSlug("example-slug");

        $this->recipeRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with([
                'slug' => "example-slug",
                'isActive' => true,
            ])
            ->willReturn($recipe);

        $result = $this->recipeService->getRecipeBySlug("example-slug");

        $this->assertEquals($result, $recipe);
    }

    public function testGetRecipeBySlugWithBadSlug()
    {
        $this->recipeRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with([
                'slug' => "example-bad-slug",
                'isActive' => true,
            ])
            ->willReturn(null);

        $result = $this->recipeService->getRecipeBySlug("example-bad-slug");

        $this->assertNull($result);
    }

    public function testDeleteRecipe()
    {
        $recipe = new Recipe();
        $recipe->setIsActive(false);
        $recipe->setDeletedAt();

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $result = $this->recipeService->deleteRecipe($recipe);

        $this->assertTrue($result);
    }

    public function testChangeInformation()
    {
        $recipe = new Recipe();
        $recipe->setName('Example name');
        $recipe->setDescription('Example description');
        $recipe->setLevel(1);
        $recipe->setTime(35);
        $recipe->setSlug('example-slug');
        $recipe->setIsActive(true);

        $this->recipeRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with([
                'slug' => 'example-slug',
                'isActive' => true
            ])
            ->willReturn($recipe);

        $recipe->setName('Example new name');
        $recipe->setDescription('Example new description');
        $recipe->setLevel(3);
        $recipe->setTime(45);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $result = $this->recipeService->changeInformation($recipe, "example-slug");

        $this->assertTrue($result);
    }

    public function testChangeInformationWithBadSlug()
    {
        $recipe = new Recipe();
        $recipe->setName('Example name');
        $recipe->setDescription('Example description');
        $recipe->setLevel(1);
        $recipe->setTime(35);
        $recipe->setSlug('example-bad-slug');
        $recipe->setIsActive(true);

        $this->recipeServiceMock
            ->expects($this->any())
            ->method('getRecipeBySlug')
            ->with('example-bad-slug')
            ->willReturn(null);

        $result = $this->recipeService->changeInformation($recipe, "example-slug");

        $this->assertNull($result);
    }
}
