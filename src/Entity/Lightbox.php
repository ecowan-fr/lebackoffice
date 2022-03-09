<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(
    collectionOperations: [],
    itemOperations: [
        'get' => [
            'path' => '/lightbox/{name}',
            'openapi_context' => [
                'tags' => [
                    'Internal functioning of the backoffice'
                ],
                'summary' => 'Retrieves the HTML code from a lightbox',
                'description' => ''
            ]
        ]
    ]
)]
class Lightbox {
    #[
        ApiProperty(
            identifier: true,
            description: 'Lightbox name',
            example: "welcome"
        ),
        NotBlank()
    ]
    private string $name;

    #[
        ApiProperty(
            description: 'HTML code from a lightbox',
            example: "<div class=\"boxtitle\">...<\/div>\n"
        )
    ]
    private string $html;

    public function __construct(string $name, string $html) {
        $this->name = $name;
        $this->html = $html;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getHtml(): string {
        return $this->html;
    }
}
