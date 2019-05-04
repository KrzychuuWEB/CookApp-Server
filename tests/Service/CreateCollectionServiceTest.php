<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Ingredients;
use App\Entity\Recipe;
use App\Entity\Steps;
use App\Service\CreateCollectionService;
use App\Service\FileUploaderService;
use PHPUnit\Framework\TestCase;

class CreateCollectionServiceTest extends TestCase
{
    /**
     * @var FileUploaderService|\PHPUnit_Framework_MockObject_MockObject
     */
    private $fileUploader;

    /**
     * @var CreateCollectionService
     */
    private $createCollection;

    protected function setUp()
    {
        $this->fileUploader = $this->getMockBuilder(FileUploaderService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->createCollection = new CreateCollectionService(
            $this->fileUploader
        );
    }

    public function testCreateCollectionForIngredients()
    {
        $formData = new Recipe();

        $ingredients = [];

        $ingredients[0] = new Ingredients();
        $ingredients[0]->setValue("Example value 0");
        $ingredients[0]->setUnit("Example value 0");
        $ingredients[0]->setName("Example name 0");
        $ingredients[0]->setRecipe(null);

        $ingredients[1] = new Ingredients();
        $ingredients[1]->setValue("Example value 1");
        $ingredients[1]->setUnit("Example value 1");
        $ingredients[1]->setName("Example name 1");
        $ingredients[1]->setRecipe(null);


        foreach ($ingredients as $ingredient) {
            $formData->addIngredient($ingredient);
        }

        $result = $this->createCollection->create('ingredients', $formData);

        $actual = [];
        $actual[0] = new Ingredients();
        $actual[0]->setValue("Example value 0");
        $actual[0]->setUnit("Example value 0");
        $actual[0]->setName("Example name 0");

        $actual[1] = new Ingredients();
        $actual[1]->setValue("Example value 1");
        $actual[1]->setUnit("Example value 1");
        $actual[1]->setName("Example name 1");

        $this->assertEquals($result, $actual);
    }

    public function testCreateCollectionForSteps()
    {
        $formData = new Recipe();

        $steps = [];

        $steps[0] = new Steps();
        $steps[0]->setStep(0);
        $steps[0]->setDescription("Example description 0");
        $steps[0]->setName("Example name 0");

        $steps[1] = new Steps();
        $steps[1]->setStep(1);
        $steps[1]->setDescription("Example description 1");
        $steps[1]->setName("Example name 1");


        foreach ($steps as $step) {
            $formData->addStep($step);
        }

        $result = $this->createCollection->create('steps', $formData);

        $actual = [];
        $actual[0] = new Steps();
        $actual[0]->setStep(0);
        $actual[0]->setDescription("Example description 0");
        $actual[0]->setName("Example name 0");

        $actual[1] = new Steps();
        $actual[1]->setStep(1);
        $actual[1]->setDescription("Example description 1");
        $actual[1]->setName("Example name 1");

        $this->assertEquals($result, $actual);
    }
}
