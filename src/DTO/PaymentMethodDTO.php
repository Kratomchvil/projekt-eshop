<?php
declare(strict_types=1);

final class PaymentMethodDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public float $fee,
    ) {}
}
