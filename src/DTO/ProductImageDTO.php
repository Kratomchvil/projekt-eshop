<?php
declare(strict_types=1);

final class ProductImageDTO
{
    public function __construct(
        public readonly int $id,
        public readonly int $productId,
        public readonly string $url,
        public readonly ?string $alt = null,
    ) {}
}
