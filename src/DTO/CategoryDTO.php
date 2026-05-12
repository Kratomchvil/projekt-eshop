<?php
declare(strict_types=1);

final class CategoryDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $slug,
        public readonly ?string $description = null,
        public readonly ?string $image = null,
    ) {}
}
