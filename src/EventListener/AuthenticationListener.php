<?php

declare(strict_types=1);

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTFailureEventInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthenticationListener
{
    /**
     * @param AuthenticationFailureEvent $event
     */
    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event)
    {
        $response = new JWTAuthenticationFailureResponse();
        $response->setData([
            "status" => "error",
            "data" => null,
            "message" => $event->getException()->getMessage(),
        ]);

        $event->setResponse($response);
    }

    /**
     * @param JWTFailureEventInterface $event
     */
    public function onJWTTokenFailureResponse(JWTFailureEventInterface $event)
    {
        $data = [
            "status"  => "error",
            "data" => null,
            "message" => $event->getResponse()->getMessage(),
        ];

        $response = new JsonResponse($data, 401);

        $event->setResponse($response);
    }
}
