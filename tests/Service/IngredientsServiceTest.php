<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Ingredients;
use App\Entity\Recipe;
use App\Repository\IngredientsRepository;
use App\Service\CreateCollectionService;
use App\Service\IngredientsService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class IngredientsServiceTest extends TestCase
{
    /**
     * @var EntityManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManager;

    /**
     * @var IngredientsRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $ingredientsRepository;

    /**
     * @var IngredientsService
     */
    private $ingredientsService;

    /**
     * @var CreateCollectionService|\PHPUnit_Framework_MockObject_MockObject
     */
    private $createCollection;

    protected function setUp()
    {
        $this->entityManager = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->ingredientsRepository = $this->getMockBuilder(IngredientsRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->createCollection = $this->getMockBuilder(CreateCollectionService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->ingredientsService = new IngredientsService(
            $this->entityManager,
            $this->ingredientsRepository,
            $this->createCollection
        );
    }

    protected function tearDown()
    {
        $this->entityManager = null;
        $this->ingredientsService = null;
    }

    public function testChangeIngredient()
    {
        $ingredients = new Ingredients();
        $ingredients->setName("Example name");
        $ingredients->setUnit("Example unit");
        $ingredients->setValue("Example value");

        $this->ingredientsRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with([
                'recipe' => 1,
                'id' => 1
            ])
            ->willReturn($ingredients);

        $ingredients->setName("Example new name");
        $ingredients->setValue("Example new value");
        $ingredients->setUnit("Example new unit");
        
        $this->entityManager->flush();
        
        $result = $this->ingredientsService->changeIngredient($ingredients, 1, 1);

        $this->assertTrue($result);
    }

    public function testDeleteIngredient()
    {
        $ingredient = new Ingredients();

        $this->ingredientsRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with([
                'recipe' => 1,
                'id' => 1
            ])
            ->willReturn($ingredient);

        $this->entityManager
            ->expects($this->once())
            ->method('remove');
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $result = $this->ingredientsService->deleteIngredient(1, 1);

        $this->assertTrue($result);
    }

    public function testDeleteIngredientWithBadIngredientId()
    {
        $this->ingredientsRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with([
                'recipe' => 1,
                'id' => 999
            ])
            ->willReturn(null);

        $result = $this->ingredientsService->deleteIngredient(1, 999);

        $this->assertNull($result);
    }

    public function testCreateIngredients()
    {
        $ingredientForFormData[0] = new Ingredients();
        $ingredientForFormData[0]->setName("Example name");
        $ingredientForFormData[0]->setUnit("Example unit");
        $ingredientForFormData[0]->setValue("Example value");
        $formData = new Recipe();
        $formData->addIngredient($ingredientForFormData[0]);

        $recipeForAddNewIngredient = new Recipe();
        $recipeForAddNewIngredient->setId(1);

        $this->createCollection
            ->expects($this->once())
            ->method('create')
            ->with('ingredients', $formData)
            ->willReturn($ingredientForFormData);

        $this->entityManager
            ->expects($this->any())
            ->method('persist')
            ->willReturn($ingredientForFormData);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $result = $this->ingredientsService->createIngredients($formData, $recipeForAddNewIngredient);

        $this->assertTrue($result);
    }
}
