<?php

namespace App\DataPersister;

use App\Entity\FlashMessage;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class FlashMessageDataPersister implements DataPersisterInterface {

    public function __construct(
        private readonly RequestStack $request,
        private readonly TranslatorInterface $translator
    ) {
    }

    public function supports($data): bool {
        return $data instanceof FlashMessage;
    }

    /**
     * Persists the data.
     *
     * @param FlashMessage $data
     *
     * @return object Void will not be supported in API Platform 3, an object should always be returned
     */
    public function persist($data) {

        try {
            $message = $this->translator->trans($data->getMessage(), [], $data->getDomainTranslation());
        } catch (\Throwable $th) {
            $message = $data->getMessage();
        }

        /** @var Session */
        $session = $this->request->getCurrentRequest()->getSession();
        $session->getFlashBag()->add($data->getType(), $message);
        return $data;
    }

    public function remove($data) {
        return;
    }
}
