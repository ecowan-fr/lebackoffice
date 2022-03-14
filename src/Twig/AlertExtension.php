<?php

namespace App\Twig;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AlertExtension extends AbstractExtension {

    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
    }

    public function getFunctions(): array {
        return [
            new TwigFunction('showAlert', [$this, 'showAlert']),
            new TwigFunction('showFlashMessage', [$this, 'showFlashMessage']),
        ];
    }

    public function showAlert(string $type, string $message, ?string $class = null) {
        switch ($type) {
            case 'warning':
                $icon = "<i class='fa-solid fa-circle-radiation text-yellow-500'></i>";
                $color = 'orange';
                break;
            case 'info':
                $icon = "<i class='fa-solid fa-info text-blue-500'></i>";
                $color = 'blue';
                break;
            case 'success':
                $icon = "<i class='fa-solid fa-check text-green-500'></i>";
                $color = 'green';
                break;
            case 'error':
                $icon = "<i class='fa-solid fa-circle-exclamation text-red-500'></i>";
                $color = 'red';
                break;
        }
        echo "<div class='alert $color $class'>$icon<p class='ml-2'>$message</p></div>";
    }

    public function showFlashMessage(array $flashes = []) {
        $html = "";
        $js = "<script type='application/javascript'>document.timeoutFlash = setTimeout(() => { document.querySelector('.flashbag').classList.add('animate__bounceOutUp');}, 7000);</script>";
        $jsAdd = 0;

        foreach ($flashes as $key => $value) {
            foreach ($value as $key2 => $value2) {
                switch ($key) {
                    case 'warning':
                        $icon = "<i class='fas fa-radiation-alt'></i> " . $this->translator->trans('Warning', [], 'global');
                        break;
                    case 'info':
                        $icon = "<i class='fas fa-info'></i> Info";
                        break;
                    case 'success':
                        $icon = "<i class='fas fa-check'></i> OK";
                        break;
                    case 'error':
                        $icon = "<i class='fas fa-exclamation'></i> " . $this->translator->trans('Error', [], 'validators');
                        break;
                }
                $html .= "<div class='flash $key'><div class='flex items-center'><span class='$key'>$icon</span><p>$value2</p></div></div>";
                $jsAdd++;
            }
        }
        if ($jsAdd > 0) {
            $html .= $js;
        }
        echo $html;
    }
}
