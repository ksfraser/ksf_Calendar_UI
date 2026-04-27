# Ksfraser/Superglobals

A PHP library that provides testable, dependency-injectable access to superglobals (`$_GET`, `$_POST`, `$_FILES`, etc.) following the Single Responsibility Principle.

## Installation

```bash
composer require ksfraser/superglobals
```

## Usage

### Basic Usage

```php
use Ksfraser\Superglobals\PostParameterProvider;
use Ksfraser\Superglobals\FormSubmission;

// Inject the parameter provider
$parameterProvider = new PostParameterProvider();
$formSubmission = new FormSubmission($parameterProvider);

// Use in your application logic
if ($formSubmission->hasUpload()) {
    $parser = $formSubmission->getParser();
    $bankAccount = $formSubmission->getBankAccount();
    // ... handle upload logic
}
```

### Testing

The library is designed to be easily testable by injecting mock parameter providers:

```php
use Ksfraser\Superglobals\FormSubmission;
use Ksfraser\Superglobals\ParameterProvider;

class MockParameterProvider implements ParameterProvider {
    private $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function get(string $key): ?string {
        return $this->data[$key] ?? null;
    }

    public function has(string $key): bool {
        return isset($this->data[$key]);
    }

    public function all(): array {
        return $this->data;
    }
}

// Test your form handling logic
$form = new FormSubmission(new MockParameterProvider([
    'upload' => '1',
    'parser' => 'QFX',
    'bank_account' => '123'
]));

assert($form->hasUpload() === true);
assert($form->getParser() === 'QFX');
```

## Architecture

The library follows SOLID principles:

- **Single Responsibility**: Each class has one reason to change
- **Open/Closed**: Classes are open for extension but closed for modification
- **Liskov Substitution**: Implementations can be substituted for the interface
- **Interface Segregation**: Clients depend only on methods they use
- **Dependency Inversion**: High-level modules don't depend on low-level modules

## Classes

### ParameterProvider Interface

Abstracts access to parameter arrays (like `$_GET`, `$_POST`).

### PostParameterProvider

Concrete implementation for `$_POST` superglobal.

### GetParameterProvider

Concrete implementation for `$_GET` superglobal.

### FormSubmission

Handles form submission logic with dependency injection for testability.

## Contributing

1. Fork the repository
2. Create a feature branch
3. Add tests for new functionality
4. Ensure all tests pass
5. Submit a pull request

## License

This project is licensed under the GPL-3.0-or-later License - see the LICENSE file for details.