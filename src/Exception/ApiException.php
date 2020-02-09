<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiException extends HttpException implements ApiExceptionInterface
{
    public function __construct(string $message, int $code, Exception $previous = null)
    {
        parent::__construct($code, $message, $previous);
    }
}
