<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\StringConverterService;
use PHPUnit\Framework\TestCase;

class StringConverterServiceTest extends TestCase
{
    /**
     * @var StringConverterService
     */
    private $stringConverter;

    public function setUp()
    {
        $this->stringConverter = new StringConverterService();
    }

    public function tearDown()
    {
        $this->stringConverter = null;
    }

    public function testSetUppercase()
    {
        $result = $this->stringConverter->setUppercase("example");

        $this->assertEquals($result, "EXAMPLE");
    }

    public function testAddPrefix()
    {
        $result = $this->stringConverter->addPrefix("example", "prefix_");

        $this->assertEquals($result, "prefix_example");
    }

    public function testAddPrefixWithNameHasPrefix()
    {
        $result = $this->stringConverter->addPrefix("prefix_example", "prefix_");

        $this->assertEquals($result, "prefix_example");
    }
}
