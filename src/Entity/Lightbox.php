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
                    'Fonctionnement interne du backoffice'
                ],
                'summary' => 'Récupére le contenue HTML d\'une Lightbox',
                'description' => '',
                'parameters' => [
                    [
                        'in' => 'path',
                        'name' => 'name',
                        'description' => 'Nom de la Lighbox',
                        'required' => true,
                        'schema' => [
                            'type' => 'string'
                        ]
                    ]
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Le code HTML de la Lightbox à bien été récupéré.',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'string',
                                    'example' => "<div class=\"boxtitle\">...<\/div>\n"
                                ]
                            ]
                        ]
                    ],
                    '404' => [
                        'description' => 'Cette lightbox n\'existe pas.',
                    ]
                ]
            ]
        ]
    ],
    formats: [
        'json'
    ]
)]
class Lightbox {
    #[
        ApiProperty(
            identifier: true,
            description: 'Nom de la lightbox'
        ),
        NotBlank()
    ]
    private string $name;
}
