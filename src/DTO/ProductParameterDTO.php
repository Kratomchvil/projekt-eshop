<?php
declare(strict_types=1);

final class ProductParameterDTO
{
    public function __construct(
        public int $id,
        public int $productId,
        public string $name,
        public string $value,
        public string $type, // 'select' | 'info'
    ) {}

    public function isSelectable(): bool
    {
        return $this->type === 'select';
    }
}
