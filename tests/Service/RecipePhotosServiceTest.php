<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\RecipePhotos;
use App\Repository\RecipePhotosRepository;
use App\Service\CreateCollectionService;
use App\Service\RecipePhotosService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class RecipePhotosServiceTest extends TestCase
{
    /**
     * @var EntityManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManager;

    /**
     * @var RecipePhotosRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $recipePhotosRepository;

    /**
     * @var RecipePhotosService
     */
    private $recipePhotosService;

    /**
     * @var CreateCollectionService|\PHPUnit_Framework_MockObject_MockObject
     */
    private $createCollection;

    protected function setUp()
    {
        $this->entityManager = $this->getMockBuilder(EntityManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->recipePhotosRepository = $this->getMockBuilder(RecipePhotosRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->createCollection = $this->getMockBuilder(CreateCollectionService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->recipePhotosService = new RecipePhotosService(
            $this->entityManager,
            $this->recipePhotosRepository,
            $this->createCollection
        );
    }

    protected function tearDown()
    {
        $this->entityManager = null;
        $this->recipePhotosService = null;
    }

    public function testDeletePhoto()
    {
        $recipePhotos = new RecipePhotos();

        $this->recipePhotosRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with([
                'recipe' => 1,
                'id' => 1
            ])
            ->willReturn($recipePhotos);

        $this->entityManager
            ->expects($this->once())
            ->method('remove');
        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $result = $this->recipePhotosService->deletePhoto(1, 1);

        $this->assertTrue($result);
    }
}
