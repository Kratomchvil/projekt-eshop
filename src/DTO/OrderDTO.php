<?php
declare(strict_types=1);

final class OrderDTO
{
    public function __construct(
        public int $id,
        public int $customerId,
        public float $totalPrice,
    ) {}
}
