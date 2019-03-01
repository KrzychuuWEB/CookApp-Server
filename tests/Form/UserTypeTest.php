<?php

namespace App\Tests\Form;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\Form\Test\TypeTestCase;


class UserTypeTest extends TypeTestCase
{
    public function testFormIfDataIsValid()
    {
        $formData = [
            'username' => 'ExampleUsername',
            'plainPassword' => 'ExamplePassword',
            'email' => 'ExampleEmail',
        ];

        $expected = new User();
        $expected->setUsername('ExampleUsername');
        $expected->setPlainPassword('ExamplePassword');
        $expected->setEmail('ExampleEmail');

        $form = $this->factory->create(UserType::class, null);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());
        $this->assertEquals($expected, $form->getData());
    }
}
