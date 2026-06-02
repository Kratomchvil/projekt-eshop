<?php
declare(strict_types=1);

final class ProductImageDTO
{
    public function __construct(
        public int $id,
        public int $productId,
        public string $url,
        public ?string $alt = null,
    ) {}
}
