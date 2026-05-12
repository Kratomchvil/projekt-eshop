<?php
declare(strict_types=1);

final class CategoryRepository
{
    public function __construct(private Database $db) {}

    /** @return CategoryDTO[] */
    public function getAll(): array
    {
        $rows = $this->db->pdo()->query('SELECT * FROM categories ORDER BY name')->fetchAll();
        return array_map([$this, 'map'], $rows);
    }

    public function getBySlug(string $slug): ?CategoryDTO
    {
        $stmt = $this->db->pdo()->prepare('SELECT * FROM categories WHERE slug = :slug LIMIT 1');
        $stmt->execute([':slug' => $slug]);
        $row = $stmt->fetch();
        return $row ? $this->map($row) : null;
    }

    public function getProductCount(int $categoryId): int
    {
        $stmt = $this->db->pdo()->prepare('SELECT COUNT(*) FROM products WHERE category_id = :id');
        $stmt->execute([':id' => $categoryId]);
        return (int)$stmt->fetchColumn();
    }

    private function map(array $r): CategoryDTO
    {
        return new CategoryDTO(
            (int)$r['id'],
            (string)$r['name'],
            (string)$r['slug'],
            $r['description'] ?? null,
            $r['image'] ?? null,
        );
    }
}
