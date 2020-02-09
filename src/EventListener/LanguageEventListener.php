<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LanguageEventListener implements EventSubscriberInterface
{
    private $defaultLocaleLanguage;

    public function __construct($defaultLocaleLanguage = 'en')
    {
        $this->defaultLocaleLanguage = $defaultLocaleLanguage;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $locale = $request->headers->get("accept-language");
        if ($locale) {
            $request->setLocale($locale);
        } else {
            $request->setLocale($this->defaultLocaleLanguage);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}
