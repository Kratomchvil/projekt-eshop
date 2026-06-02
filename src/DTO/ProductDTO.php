<?php
declare(strict_types=1);

final class ProductDTO
{
    public bool $hasVariants = false;

    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        public float $price,
        public ?float $oldPrice,
        public ?string $description,
        public ?string $image,
        public int $categoryId,
        public bool $featured = false,
    ) {}

    public function isOnSale(): bool
    {
        return $this->oldPrice !== null && $this->oldPrice > $this->price;
    }
}
