<?php

namespace Ksfraser\Superglobals;

/**
 * Interface for providing request parameters.
 */
interface ParameterProvider
{
    /**
     * Get a parameter value.
     */
    public function get(string $key): ?string;

    /**
     * Check if parameter exists.
     */
    public function has(string $key): bool;

    /**
     * Get all parameters.
     */
    public function all(): array;
}