<?php

namespace App\Entity;

use ApiPlatform\Metadata\Post;
use App\State\FlashMessageProcessor;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiFilter;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(
    processor: FlashMessageProcessor::class,
    operations: [
        new Post(
            uriTemplate: '/flash',
            openapiContext: [
                'tags' => [
                    'Internal functioning of the backoffice'
                ],
                'summary' => 'Create a flash message',
                'description' => ''
            ]
        )
    ]
)]
class FlashMessage {
    #[ApiProperty(description: 'Type', identifier: true, example: 'success', openapiContext: ['enum' => ['success', error::class, 'info', 'warning']])]
    private string $type;

    #[ApiProperty(description: 'Message')]
    private string $message;

    #[ApiProperty(description: 'The translation domain', default: 'default')]
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
