<?php
declare(strict_types=1);

final class ProductRepository
{
    public function __construct(private Database $db) {}

    /** @return ProductDTO[] */
    public function getFeatured(int $limit = 6): array
    {
        $stmt = $this->db->pdo()->prepare('SELECT * FROM products WHERE featured = 1 ORDER BY id LIMIT :lim');
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $this->mapMany($stmt->fetchAll());
    }

    /** @return ProductDTO[] */
    public function getByCategory(int $categoryId): array
    {
        $stmt = $this->db->pdo()->prepare('SELECT * FROM products WHERE category_id = :id ORDER BY name');
        $stmt->execute([':id' => $categoryId]);
        return $this->mapMany($stmt->fetchAll());
    }

    /** @return ProductDTO[] */
    public function getByCategorySlug(string $slug): array
    {
        $stmt = $this->db->pdo()->prepare('
            SELECT p.* FROM products p
            JOIN categories c ON c.id = p.category_id
            WHERE c.slug = :slug ORDER BY p.name
        ');
        $stmt->execute([':slug' => $slug]);
        return $this->mapMany($stmt->fetchAll());
    }

    public function getBySlug(string $slug): ?ProductDTO
    {
        $stmt = $this->db->pdo()->prepare('SELECT * FROM products WHERE slug = :slug LIMIT 1');
        $stmt->execute([':slug' => $slug]);
        $row = $stmt->fetch();
        if (!$row) return null;
        $product = $this->map($row);
        $product->hasVariants = $this->hasSelectableParameters($product->id);
        return $product;
    }

    public function getById(int $id): ?ProductDTO
    {
        $stmt = $this->db->pdo()->prepare('SELECT * FROM products WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        if (!$row) return null;
        $product = $this->map($row);
        $product->hasVariants = $this->hasSelectableParameters($product->id);
        return $product;
    }

    /** @return ProductImageDTO[] */
    public function getImages(int $productId): array
    {
        $stmt = $this->db->pdo()->prepare('SELECT * FROM product_images WHERE product_id = :id ORDER BY id');
        $stmt->execute([':id' => $productId]);
        $out = [];
        foreach ($stmt->fetchAll() as $r) {
            $out[] = new ProductImageDTO((int)$r['id'], (int)$r['product_id'], (string)$r['url'], $r['alt'] ?? null);
        }
        return $out;
    }

    /** @return ProductParameterDTO[] */
    public function getParameters(int $productId): array
    {
        $stmt = $this->db->pdo()->prepare('SELECT * FROM product_parameters WHERE product_id = :id ORDER BY id');
        $stmt->execute([':id' => $productId]);
        $out = [];
        foreach ($stmt->fetchAll() as $r) {
            $out[] = new ProductParameterDTO(
                (int)$r['id'], (int)$r['product_id'],
                (string)$r['name'], (string)$r['value'], (string)$r['type']
            );
        }
        return $out;
    }

    /** @return ProductDTO[] */
    public function search(string $query): array
    {
        $like = '%' . $query . '%';
        $stmt = $this->db->pdo()->prepare('
            SELECT * FROM products
            WHERE name LIKE :q OR description LIKE :q
            ORDER BY name
        ');
        $stmt->execute([':q' => $like]);
        return $this->mapMany($stmt->fetchAll());
    }

    private function hasSelectableParameters(int $productId): bool
    {
        $stmt = $this->db->pdo()->prepare("SELECT 1 FROM product_parameters WHERE product_id = :id AND type = 'select' LIMIT 1");
        $stmt->execute([':id' => $productId]);
        return (bool)$stmt->fetchColumn();
    }

    /** @return ProductDTO[] */
    private function mapMany(array $rows): array
    {
        return array_map([$this, 'map'], $rows);
    }

    private function map(array $r): ProductDTO
    {
        return new ProductDTO(
            (int)$r['id'],
            (string)$r['name'],
            (string)$r['slug'],
            (float)$r['price'],
            isset($r['old_price']) && $r['old_price'] !== null ? (float)$r['old_price'] : null,
            $r['description'] ?? null,
            $r['image'] ?? null,
            (int)$r['category_id'],
            (bool)($r['featured'] ?? 0),
        );
    }
}
