<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Repository\PermissionRepository;
use App\Service\StringConverterService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ConstraintsPermissionNotFoundValidator extends ConstraintValidator
{
    /**
     * @var PermissionRepository
     */
    private $permissionRepository;

    /**
     * @var StringConverterService
     */
    private $stringConverter;

    /**
     * ConstraintsPermissionNotFoundValidator constructor.
     *
     * @param PermissionRepository $permissionRepository
     * @param StringConverterService $stringConverter
     */
    public function __construct(
        PermissionRepository $permissionRepository,
        StringConverterService $stringConverter
    ) {
        $this->permissionRepository = $permissionRepository;
        $this->stringConverter = $stringConverter;
    }

    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ConstraintsPermissionNotFound) {
            throw new UnexpectedTypeException($constraint, ConstraintsPermissionNotFound::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        $permission = $this->stringConverter->setUppercase($value);
        $permission = $this->stringConverter->addPrefix($permission, "ROLE_");

        if (!$this->permissionRepository->findBy(['name' => $permission])) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
