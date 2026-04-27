<?php

namespace Ksfraser\Superglobals;

/**
 * Provides parameters from $_GET.
 */
class GetParameterProvider implements ParameterProvider
{
    public function get(string $key): ?string
    {
        return isset($_GET[$key]) ? (string)$_GET[$key] : null;
    }

    public function has(string $key): bool
    {
        return isset($_GET[$key]);
    }

    public function all(): array
    {
        return $_GET;
    }
}