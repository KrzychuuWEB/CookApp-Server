<?php

namespace App\Controller;

use App\Service\FormErrorConverter;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractApiController extends AbstractFOSRestController
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var FormErrorConverter
     */
    private $formErrorConverter;

    /**
     * AbstractApiController constructor.
     * @param TranslatorInterface $translator
     * @param FormErrorConverter $converter
     */
    public function __construct(
        TranslatorInterface $translator,
        FormErrorConverter $converter
    ) {
        $this->translator = $translator;
        $this->formErrorConverter = $converter;
    }


    public function createRestResponse(bool $success, ?array $data, ?string $message, int $status = Response::HTTP_OK, array $groups = []): Response
    {
        return $this->json([
            "status" => $success ? "success" : "error",
            "data" => $data,
            "message" => $message
        ], $status);
    }

    /**
     * @param string $string
     * @return string
     */
    public function getTranslate(string $string): string
    {
        return $this->translator->trans($string);
    }

    /**
     * @param FormInterface $form
     * @return array
     */
    public function getErrorsFromForm(FormInterface $form): array
    {
        return $this->formErrorConverter->convertFormErrorToArray($form);
    }
}
