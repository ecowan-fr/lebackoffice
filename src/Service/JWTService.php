<?php

namespace App\Service;

use Webauthn\MetadataService\Statement\StatusReport;
use Webauthn\MetadataService\Statement\MetadataStatement;

class JWTService {

    private object $JWTDecoded;

    public function __construct(
        private readonly string $kernel_dir
    ) {
        $JWT = file_get_contents($kernel_dir . DIRECTORY_SEPARATOR . 'blobWebAuthn_20220601.jwt');
        $JWTJson = base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $JWT)[1])));
        $this->JWTDecoded = json_decode($JWTJson);
    }

    private function searchForId($id, $array) {
        foreach ($array as $key => $val) {
            if (property_exists($val, 'aaguid') && $val->aaguid === $id) {
                return $key;
            }
        }
        return null;
    }

    public function getMetadataStatement(string $aaguid): ?MetadataStatement {
        $id = $this->searchForId($aaguid, $this->JWTDecoded->entries);

        if (!is_null($id)) {
            $metadata = MetadataStatement::createFromString(json_encode($this->JWTDecoded->entries[$id]->metadataStatement));
            return $metadata;
        } else {
            return null;
        }
    }

    public function getStatusReports(string $aaguid): array {
        $id = $this->searchForId($aaguid, $this->JWTDecoded->entries);
        $data = [];
        foreach ($this->JWTDecoded->entries[$id]->statusReports as $key => $value) {
            $data[] = StatusReport::createFromArray((array) $value);
        }
        return $data;
    }
}
