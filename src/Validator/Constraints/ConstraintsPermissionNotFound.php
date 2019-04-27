<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ConstraintsPermissionNotFound extends Constraint
{
    /**
     * @var string
     */
    public $message = "The permission name is not found";
}
