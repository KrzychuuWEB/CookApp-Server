<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Recipe;
use App\Entity\Steps;
use App\Repository\StepsRepository;
use App\Service\CreateCollectionService;
use App\Service\StepsService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class StepsServiceTest extends TestCase
{
    /**
     * @var EntityManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManager;

    /**
     * @var StepsRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stepsRepository;

    /**
     * @var StepsService
     */
    private $stepsService;

    /**
     * @var CreateCollectionService|\PHPUnit_Framework_MockObject_MockObject
     */
    private $createCollection;

    protected function setUp()
    {
        $this->entityManager = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->stepsRepository = $this->getMockBuilder(StepsRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->createCollection = $this->getMockBuilder(CreateCollectionService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->stepsService = new StepsService(
            $this->entityManager,
            $this->stepsRepository,
            $this->createCollection
        );
    }

    protected function tearDown()
    {
        $this->entityManager = null;
        $this->stepsRepository = null;
        $this->stepsService = null;
        $this->createCollection = null;
    }

    public function testChangeStep()
    {
        $steps = new Steps();
        $steps->setName("Example name");
        $steps->setDescription("Example description");
        $steps->setStep(1);

        $this->stepsRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with([
                'recipe' => 1,
                'id' => 1
            ])
            ->willReturn($steps);

        $steps->setName("Example new name");
        $steps->setDescription("Example description");
        $steps->setStep(1);
        
        $this->entityManager->flush();
        
        $result = $this->stepsService->changeStep($steps, 1, 1);

        $this->assertTrue($result);
    }

    public function testDeleteStep()
    {
        $steps = new Steps();

        $this->stepsRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with([
                'recipe' => 1,
                'id' => 1
            ])
            ->willReturn($steps);

        $this->entityManager
            ->expects($this->once())
            ->method('remove');
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $result = $this->stepsService->deleteStep(1, 1);

        $this->assertTrue($result);
    }

    public function testDeleteStepWithBadStepId()
    {
        $this->stepsRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with([
                'recipe' => 1,
                'id' => 999
            ])
            ->willReturn(null);

        $result = $this->stepsService->deleteStep(1, 999);

        $this->assertNull($result);
    }

    public function testCreateSteps()
    {
        $stepsForFormData[0] = new Steps();
        $stepsForFormData[0]->setName("Example name");
        $stepsForFormData[0]->setDescription("Example description");
        $stepsForFormData[0]->setStep(1);
        $formData = new Recipe();
        $formData->addStep($stepsForFormData[0]);

        $recipeForAddNewStep = new Recipe();
        $recipeForAddNewStep->setId(1);

        $this->createCollection
            ->expects($this->once())
            ->method('create')
            ->with('steps', $formData)
            ->willReturn($stepsForFormData);

        $this->entityManager
            ->expects($this->any())
            ->method('persist')
            ->willReturn($stepsForFormData);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $result = $this->stepsService->createSteps($formData, $recipeForAddNewStep);

        $this->assertTrue($result);
    }
}
