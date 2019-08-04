<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Form\FormInterface;

class FormErrorConverter
{
    public function convertFormErrorToArray(FormInterface $form): array
    {
        $errors = [];

        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->convertFormErrorToArray($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }

        return $errors;
    }
}
