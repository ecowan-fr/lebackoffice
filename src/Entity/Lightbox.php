<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiFilter;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(operations: [new Get(uriTemplate: '/lightbox/{name}', openapiContext: ['tags' => ['Internal functioning of the backoffice'], 'summary' => 'Retrieves the HTML code from a lightbox', 'description' => '', 'security' => [['bearerAuth' => []]]])])]
class Lightbox {
    #[ApiProperty(description: 'Lightbox name', identifier: true, example: 'welcome')]
    private string $name;
    #[ApiProperty(description: 'HTML code from a lightbox', example: '<div class="boxtitle">...<\\/div>
')]
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
