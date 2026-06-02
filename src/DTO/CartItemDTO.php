<?php
declare(strict_types=1);

final class CartItemDTO
{
    public function __construct(
        public int $productId,
        public string $productName,
        public float $unitPrice,
        public int $quantity,
        public string $variant = '',
        public ?string $image = null,
    ) {}

    public function getSubtotal(): float
    {
        return $this->unitPrice * $this->quantity;
    }
}
