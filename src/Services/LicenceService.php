<?php

namespace App\Services;

use \DateTime;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * LA MODIFICATION DE CETTE CLASSE AFIN D'AVOIR ACCESS AUX SERVICES PRÉSENTS
 * UNIQUEMENT DANS L'ÉDITION PREMIUM, EST PROHIBÉE ET RÉPRÉHENSIBLE AU VU DE LA LOI
 * EN VERTUE DES ARTICLES 5 ET ARTICLES 6 DES CONDITIONS GENERALES D'UTILISATION
 * @see https://github.com/ecowan-fr/lebackoffice/blob/main/terms.md
 */
class LicenceService {

    private string $licence;
    public string $message;
    private stdClass $infos;

    public function __construct(
        private readonly TranslatorInterface $translator
    ) {
        $this->licence = $_ENV['APP_LICENCE'];
    }

    /**
     * @param string $ip
     * @param string $range
     * @return bool
     */
    private function cidr_match(string $ip, string $range): bool {
        list($subnet, $bits) = explode('/', $range);
        if ($bits === null) {
            $bits = 32;
        }
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet &= $mask;
        return ($ip & $mask) == $subnet;
    }

    /**
     * @param Request $request
     * @return boolean
     */
    public function isValid(Request $request): bool {
        $session = $request->getSession();
        $licenceOnSession = $session->get('licence');

        if (!is_null($licenceOnSession) && json_decode($licenceOnSession)->licence === $this->licence) {
            return true;
        } else {
            if ($this->verifValid($request)) {
                $session->set('licence', json_encode([
                    'licence' => $this->licence,
                    'infos' => $this->infos
                ]));
                return true;
            }
            return false;
        }
    }

    /**
     * @param Request $request
     * @return boolean
     */
    private function verifValid(Request $request): bool {
        if ($this->licence === '') {
            $this->message = $this->translator->trans('No license is found.');
            return false;
        }

        $data = str_split(base64_decode($this->licence), 512);

        $decrypted = '';
        $f5sd654f4 = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . base64_decode('UzUxRjVFMUc1Q0M1UjhHLnBlbQ==');
        if (!is_readable($f5sd654f4)) {
            $this->message = $this->translator->trans('Unable to read decryption key.');
            return false;
        } else {
            $fp = fopen($f5sd654f4, "r");
            $read = fread($fp, filesize($f5sd654f4));
            fclose($fp);
            $f4e864s61c1 = openssl_get_publickey($read);

            if (!$f4e864s61c1) {
                $this->message = $this->translator->trans('The decryption key is invalid.');
                return false;
            } else {
                foreach ($data as $value) {
                    $partial = '';
                    $decryptionOK = openssl_public_decrypt($value, $partial, $f4e864s61c1);
                    if ($decryptionOK === false) {
                        $decrypted = false;
                        break;
                    }
                    $decrypted .= $partial;
                }

                if (!$decrypted) {
                    $this->message = $this->translator->trans('Failed to decrypt license.');
                    return false;
                } else {
                    $checkHash = '';
                    foreach (json_decode($decrypted) as $key => $value) {
                        if ($key != 'hash') {
                            $checkHash .= $value;
                        }
                    }

                    if (hash('sha512', $checkHash) != json_decode($decrypted)->hash) {
                        $this->message = $this->translator->trans('The license is invalid.');
                        return false;
                    } else {
                        $creation = new Datetime(json_decode($decrypted)->created);
                        $expiration = new Datetime(json_decode($decrypted)->expire);
                        $now = new Datetime();
                        if ($now->diff($expiration)->invert || !$now->diff($creation)->invert) {
                            $this->message = $this->translator->trans('The license is invalid.');
                            return false;
                        } else {
                            if (!is_null($request->server->get('SERVER_ADDR')) && !$this->cidr_match($request->server->get('SERVER_ADDR') === '::1' ? "127.0.0.1" : $request->server->get('SERVER_ADDR'), json_decode($decrypted)->ip)) {
                                $this->message = $this->translator->trans('The license is invalid.');
                                return false;
                            } else {
                                $host = $request->getHost() === 'localhost' ? '127.0.0.1' : $request->getHost();
                                if (!str_contains($host, json_decode($decrypted)->url)) {
                                    $this->message = $this->translator->trans('The license is invalid.');
                                    return false;
                                } else {
                                    $this->infos = json_decode($decrypted);
                                    return true;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
