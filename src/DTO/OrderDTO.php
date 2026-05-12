<?php
declare(strict_types=1);

final class OrderDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $customerId,
        public readonly float $totalPrice,
    ) {}
}
