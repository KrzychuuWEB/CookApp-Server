<?php

declare(strict_types=1);

namespace App\Tests\Form;

use App\Entity\Accounts;
use App\Form\AccountType;
use Symfony\Component\Form\Test\TypeTestCase;

class AccountTypeTest extends TypeTestCase
{
    public function testFormIfDataIsValid()
    {
        $formData = [
            'firstName' => 'Example',
            'lastName' => 'Example',
            'age' => 0,
            'hobby' => 'Example',
            'country' => 'Example',
            'city' => 'Example',
            'aboutMe' => 'Example',
        ];

        $expected = new Accounts();
        $expected->setFirstName('Example');
        $expected->setLastName('Example');
        $expected->setAge(0);
        $expected->setHobby('Example');
        $expected->setCountry('Example');
        $expected->setCity('Example');
        $expected->setAboutMe('Example');

        $form = $this->factory->create(AccountType::class, null);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());
        $this->assertEquals($expected, $form->getData());
    }
}
