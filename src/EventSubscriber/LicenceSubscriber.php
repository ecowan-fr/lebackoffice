<?php

namespace App\EventSubscriber;

use Exception;
use App\Services\Licence;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * LA MODIFICATION DE CETTE CLASSE AFIN D'AVOIR ACCESS AUX SERVICES PRÉSENTS
 * UNIQUEMENT DANS L'ÉDITION PREMIUM, EST PROHIBÉE ET RÉPRÉHENSIBLE AU VU DE LA LOI
 * EN VERTUE DES ARTICLES 5 ET ARTICLES 6 DES CONDITIONS GENERALES D'UTILISATION
 * @see https://github.com/ecowan-fr/lebackoffice/blob/main/terms.md
 */
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