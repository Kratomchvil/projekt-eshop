<?php
declare(strict_types=1);

final class ProductParameterDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $productId,
        public readonly string $name,
        public readonly string $value,
        public readonly string $type, // 'select' | 'info'
    ) {}

    public function isSelectable(): bool
    {
        return $this->type === 'select';
    }
}
