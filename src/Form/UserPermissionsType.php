<?php

declare(strict_types=1);

namespace App\Form;

use App\Validator\Constraints\ConstraintsPermissionNotFound;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserPermissionsType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * UserPermissionsType constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('permission', TextType::class, [
                'constraints' => [
                    new ConstraintsPermissionNotFound([
                        'message' => $this->translator->trans('permission.not.found')
                    ])
                ]
            ])
        ;
    }
}
