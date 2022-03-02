<?php

namespace App\Services;

use \DateTime;
use Symfony\Component\HttpFoundation\Request;

/**
 * LA MODIFICATION DE CETTE CLASSE AFIN D'AVOIR ACCESS AUX SERVICES PRÉSENTS
 * UNIQUEMENT DANS L'ÉDITION PREMIUM, EST PROHIBÉE ET RÉPRÉHENSIBLE AU VU DE LA LOI
 * EN VERTUE DES ARTICLES 5 ET ARTICLES 6 DES CONDITIONS GENERALES D'UTILISATION
 * @see https://github.com/ecowan-fr/lebackoffice/blob/main/terms.md
 */
class Licence {

    private string $licence;

    public function __construct() {
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
     * @return boolean|string
     */
    public function isValid(Request $request): bool|string {
        if ($this->licence === '') {
            return "Aucune licence n'est présente";
        }

        $data = str_split(base64_decode($this->licence), 512);

        $decrypted = '';
        $f5sd654f4 = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . base64_decode('UzUxRjVFMUc1Q0M1UjhHLnBlbQ==');
        if (!is_readable($f5sd654f4)) {
            return "Impossible de lire la clé de déchiffrement.";
        } else {
            $fp = fopen($f5sd654f4, "r");
            $read = fread($fp, filesize($f5sd654f4));
            fclose($fp);
            $f4e864s61c1 = openssl_get_publickey($read);

            if (!$f4e864s61c1) {
                return "La clé de déchiffrement n'est pas valide.";
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
                    return "Impossible de déchiffrer la licence.";
                } else {
                    $checkHash = '';
                    foreach (json_decode($decrypted) as $key => $value) {
                        if ($key != 'hash') {
                            $checkHash .= $value;
                        }
                    }

                    if (hash('sha512', $checkHash) != json_decode($decrypted)->hash) {
                        return "La licence n'est pas valide.";
                    } else {
                        $creation = new Datetime(json_decode($decrypted)->created);
                        $expiration = new Datetime(json_decode($decrypted)->expire);
                        $now = new Datetime();
                        if ($now->diff($expiration)->invert || !$now->diff($creation)->invert) {
                            return "La licence n'est pas valide.";
                        } else {
                            if (!is_null($request->server->get('SERVER_ADDR')) && !$this->cidr_match($request->server->get('SERVER_ADDR') === '::1' ? "127.0.0.1" : $request->server->get('SERVER_ADDR'), json_decode($decrypted)->ip)) {
                                return "La licence n'est pas valide.";
                            } else {
                                $host = $request->getHost() === 'localhost' ? '127.0.0.1' : $request->getHost();
                                if (!str_contains($host, json_decode($decrypted)->url)) {
                                    return "La licence n'est pas valide.";
                                } else {
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
