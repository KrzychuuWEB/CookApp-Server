<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Exception\ApiExceptionInterface;
use Psr\Log\LoggerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ExceptionListener constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        if (!$event->getException() instanceof ApiExceptionInterface) {
            return;
        }

        $response = new JsonResponse(['status' => "error", 'data' => null, 'message' => $event->getException()->getMessage()], $event->getException()->getStatusCode());

        $event->setResponse($response);

        $this->saveLog($event);
    }

    private function saveLog(ExceptionEvent $exception)
    {
        $log = [
            'code' => $exception->getException()->getStatusCode(),
            'message' => $exception->getException()->getMessage(),
            'called' => [
                'file' => $exception->getException()->getTrace()[0]['file'],
                'line' => $exception->getException()->getTrace()[0]['line'],
            ],
            'occurred' => [
                'file' => $exception->getException()->getFile(),
                'line' => $exception->getException()->getLine(),
            ],
        ];

        if ($exception->getException()->getPrevious() instanceof Exception) {
            $log += [
                'previous' => [
                    'message' => $exception->getException()->getPrevious()->getMessage(),
                    'exception' => get_class($exception->getException()->getPrevious()),
                    'file' => $exception->getException()->getPrevious()->getFile(),
                    'line' => $exception->getException()->getPrevious()->getLine(),
                ],
            ];
        }

        $this->logger->error(json_encode($log));
    }
}
