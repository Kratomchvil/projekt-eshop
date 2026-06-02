<?php
declare(strict_types=1);

final class CategoryDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        public ?string $description = null,
        public ?string $image = null,
    ) {}
}
