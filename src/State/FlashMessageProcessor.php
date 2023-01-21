<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class FlashMessageProcessor implements ProcessorInterface {

    public function __construct(
        private readonly RequestStack $request,
        private readonly TranslatorInterface $translator
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void {
        try {
            $message = $this->translator->trans($data->getMessage(), [], $data->getDomainTranslation());
        } catch (\Throwable $th) {
            $message = $data->getMessage();
        }

        /** @var Session */
        $session = $this->request->getCurrentRequest()->getSession();
        $session->getFlashBag()->add($data->getType(), $message);
    }
}
