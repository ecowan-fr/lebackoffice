<?php

namespace App\EventSubscriber;

use Exception;
use App\Services\Licence;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LicenceSubscriber implements EventSubscriberInterface {

    public function __construct(
        private readonly Licence $licence
    ) {
    }

    public static function getSubscribedEvents() {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(ControllerEvent $event) {
        if (is_array($event->getController())) {
            $licence = $this->licence->isValid($event->getRequest());
            if ($licence !== true) {
                throw new Exception($licence);
            }
        }
    }
}
