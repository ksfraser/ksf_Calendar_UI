<?php

namespace Ksfraser\Superglobals;

/**
 * Provides parameters from $_POST.
 */
class PostParameterProvider implements ParameterProvider
{
    public function get(string $key): ?string
    {
        return isset($_POST[$key]) ? (string)$_POST[$key] : null;
    }

    public function has(string $key): bool
    {
        return isset($_POST[$key]);
    }

    public function all(): array
    {
        return $_POST;
    }
}