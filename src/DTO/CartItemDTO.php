<?php
declare(strict_types=1);

final class CartItemDTO
{
    public function __construct(
        public readonly int $productId,
        public readonly string $productName,
        public readonly float $unitPrice,
        public readonly int $quantity,
        public readonly string $variant = '',
        public readonly ?string $image = null,
    ) {}

    public function getSubtotal(): float
    {
        return $this->unitPrice * $this->quantity;
    }
}
