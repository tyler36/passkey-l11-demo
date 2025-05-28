<?php

namespace App\Support;

use Webauthn\AttestationStatement\AttestationStatementSupportManager;
use Webauthn\Denormalizer\WebauthnSerializerFactory;

/**
 * Class JsonSerializer.
 */
class JsonSerializer
{
    public static function serialize(object $data): string
    {
        return (new WebauthnSerializerFactory(AttestationStatementSupportManager::create()))
            ->create()
            ->serialize($data, 'json');
    }

    /**
     * @template TReturn
     *
     * @param class-string<TReturn> $into
     *
     * @return TReturn
     */
    public static function deserialize(string $json, string $into): mixed
    {
        return (new WebauthnSerializerFactory(AttestationStatementSupportManager::create()))
            ->create()
            ->deserialize($json, $into, 'json');
    }
}
