<?php

namespace App\Service;

use Symfony\Component\Form\FormInterface;

class FormErrorsConverter
{
    public function convertErrorsFromFrom(FormInterface $form)
    {
        $errors = [];

        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->convertErrorsFromFrom($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }

        return $errors;
    }
}