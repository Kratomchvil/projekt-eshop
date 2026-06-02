<?php
declare(strict_types=1);

final class ShippingMethodDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public float $price,
        public string $deliveryDays,
    ) {}

    public function isFree(): bool
    {
        return $this->price <= 0.0;
    }
}
