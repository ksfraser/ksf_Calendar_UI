<?php

namespace Ksfraser\Superglobals;

/**
 * Represents a form submission with POST data.
 */
class FormSubmission
{
    private $parameterProvider;

    public function __construct(ParameterProvider $parameterProvider)
    {
        $this->parameterProvider = $parameterProvider;
    }

    /**
     * Get parser selection.
     */
    public function getParser(): ?string
    {
        return $this->parameterProvider->get('parser');
    }

    /**
     * Get bank account.
     */
    public function getBankAccount(): ?string
    {
        return $this->parameterProvider->get('bank_account');
    }

    /**
     * Get uploaded files.
     */
    public function getFiles(): array
    {
        return $_FILES['files'] ?? [];
    }

    /**
     * Check if upload button was pressed.
     */
    public function hasUpload(): bool
    {
        return $this->parameterProvider->get('upload') !== null;
    }

    /**
     * Check if import button was pressed.
     */
    public function hasImport(): bool
    {
        return $this->parameterProvider->get('import') !== null;
    }

    /**
     * Get the current state.
     */
    public function getState(): ?string
    {
        return $this->parameterProvider->get('state');
    }

    /**
     * Get any parameter.
     */
    public function get(string $key): ?string
    {
        return $this->parameterProvider->get($key);
    }
}