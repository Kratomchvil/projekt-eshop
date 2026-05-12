<?php
declare(strict_types=1);

final class Validator
{
    /** @var array<string,string> */
    private array $errors = [];

    public function required(string $field, ?string $value, string $message): self
    {
        if (!isset($this->errors[$field]) && trim((string)$value) === '') {
            $this->errors[$field] = $message;
        }
        return $this;
    }

    public function email(string $field, ?string $value, string $message): self
    {
        if (!isset($this->errors[$field]) && $value !== null && $value !== ''
            && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = $message;
        }
        return $this;
    }

    public function minLength(string $field, ?string $value, int $min, string $message): self
    {
        if (!isset($this->errors[$field]) && $value !== null && mb_strlen($value) < $min) {
            $this->errors[$field] = $message;
        }
        return $this;
    }

    public function maxLength(string $field, ?string $value, int $max, string $message): self
    {
        if (!isset($this->errors[$field]) && $value !== null && mb_strlen($value) > $max) {
            $this->errors[$field] = $message;
        }
        return $this;
    }

    public function pattern(string $field, ?string $value, string $regex, string $message): self
    {
        if (!isset($this->errors[$field]) && $value !== null && $value !== ''
            && !preg_match($regex, $value)) {
            $this->errors[$field] = $message;
        }
        return $this;
    }

    /** @param array<int,string|int> $allowed */
    public function in(string $field, $value, array $allowed, string $message): self
    {
        if (!isset($this->errors[$field]) && !in_array($value, $allowed, false)) {
            $this->errors[$field] = $message;
        }
        return $this;
    }

    public function isValid(): bool
    {
        return $this->errors === [];
    }

    /** @return array<string,string> */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getError(string $field): ?string
    {
        return $this->errors[$field] ?? null;
    }

    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]);
    }
}
