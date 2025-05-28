<?php

namespace App\Support;

use Webauthn\AttestationStatement\AttestationStatementSupportManager;
use Webauthn\Denormalizer\WebauthnSerializerFactory;
use Webauthn\PublicKeyCredential;

/**
 * Class JsonSerializer.
 */
class JsonSerializer
{
    public static function serialize(object $data): string
    {
        return (new WebauthnSerializerFactory(AttestationStatementSupportManager::create()))
            ->create()
            ->deserialize($data, 'json');
    }

    public static function deserialize(string $json, string $into): PublicKeyCredential
    {
        return (new WebauthnSerializerFactory(AttestationStatementSupportManager::create()))
            ->create()
            ->deserialize($json, $into, 'json');
    }
}
