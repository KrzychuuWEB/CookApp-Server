<?php

declare(strict_types=1);

namespace App\Tests\Form;

use App\Entity\Permission;
use App\Form\PermissionUpdateType;
use Symfony\Component\Form\Test\TypeTestCase;

class PermissionUpdateTypeTest extends TypeTestCase
{
    public function testFormIfDataIsValid()
    {
        $formData = [
            'name' => 'Example',
        ];

        $expected = new Permission();
        $expected->setName('Example');

        $form = $this->factory->create(PermissionUpdateType::class, null);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());
        $this->assertEquals($expected, $form->getData());
    }
}
