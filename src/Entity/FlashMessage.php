<?php

namespace App\Entity;

use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(
    collectionOperations: [
        'post' => [
            'path' => '/flash',
            'openapi_context' => [
                'tags' => [
                    'Internal functioning of the backoffice'
                ],
                'summary' => 'Create a flash message',
                'description' => ''
            ]
        ]
    ],
    itemOperations: [
        'get' => [
            'controller' => NotFoundAction::class,
            'read' => false,
            'output' => false
        ]
    ]
)]
class FlashMessage {
    #[
        ApiProperty(
            identifier: true,
            description: 'Type',
            example: "success",
            openapiContext: [
                'enum' => ["success", "error", "info", "warning"]
            ]
        ),
        NotBlank(),
        Choice(["success", "error", "info", "warning"])
    ]
    private string $type;

    #[
        ApiProperty(
            description: 'Message'
        )
    ]
    private string $message;

    #[
        ApiProperty(
            description: 'The translation domain',
            default: 'default'
        )
    ]
    private string $domainTranslation = "default";

    public function __construct(string $type, string $message, string $domainTranslation = "default") {
        $this->type = $type;
        $this->message = $message;
        $this->domainTranslation = $domainTranslation;
    }

    public function getType(): string {
        return $this->type;
    }

    public function getMessage(): string {
        return $this->message;
    }

    public function getDomainTranslation(): string {
        return $this->domainTranslation;
    }
}
