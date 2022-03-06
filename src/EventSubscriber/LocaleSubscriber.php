<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface {

    private string $defaultLocale = 'fr';

    public static function getSubscribedEvents() {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }

    public function onKernelRequest(RequestEvent $event) {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }

        $session = $request->getSession();
        if ($locale = $request->attributes->get('_locale')) {
            $session->set('_locale', $locale);
        } else {
            $request->setLocale($session->get('_locale', $this->defaultLocale));
        }
    }
}
