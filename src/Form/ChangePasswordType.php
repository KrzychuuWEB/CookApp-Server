<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("password", RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match',
                'first_options'  => [
                    'label' => 'newPassword',
                    'constraints' => [
                        new Length(['min' => 8]),
                        new NotBlank(),
                    ]
                ],
                'second_options' => [
                    'label' => 'repeatNewPassword',
                    'constraints' => [
                        new Length(['min' => 8]),
                        new NotBlank(),
                    ]
                ],
                'required' => true,
            ])
            ->add("oldPassword", PasswordType::class, [
                'required' => true,
                'constraints' => [
                    new Length(['min' => 8]),
                    new NotBlank(),
                ]
            ])
        ;
    }
}
