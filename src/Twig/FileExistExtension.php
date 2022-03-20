<?php

namespace App\Twig;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

class FileExistExtension extends AbstractExtension {

    public function __construct(
        private readonly string $rootPath
    ) {
    }

    public function getFunctions(): array {
        return [
            new TwigFunction('fileExist', [$this, 'fileExist']),
        ];
    }

    public function fileExist(string $value): bool {
        $value = $this->rootPath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $value);
        return file_exists($value);
    }
}
