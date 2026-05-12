<?php
declare(strict_types=1);

final class ProductDTO
{
    public bool $hasVariants = false;

    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly float $price,
        public readonly ?float $oldPrice,
        public readonly ?string $description,
        public readonly ?string $image,
        public readonly int $categoryId,
        public readonly bool $featured = false,
    ) {}

    public function isOnSale(): bool
    {
        return $this->oldPrice !== null && $this->oldPrice > $this->price;
    }
}
