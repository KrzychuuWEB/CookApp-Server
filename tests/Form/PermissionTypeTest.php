<?php

declare(strict_types=1);

namespace App\Tests\Form;

use App\Entity\Permission;
use App\Form\PermissionType;
use Symfony\Component\Form\Test\TypeTestCase;

class PermissionTypeTest extends TypeTestCase
{
    public function testFormIfDataIsValid()
    {
        $formData = [
            'name' => 'Example',
        ];

        $expected = new Permission();
        $expected->setName('Example');

        $form = $this->factory->create(PermissionType::class, null);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());
        $this->assertEquals($expected, $form->getData());
    }
}
