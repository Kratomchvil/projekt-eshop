<?php
declare(strict_types=1);

final class ShippingMethodRepository
{
    public function __construct(private Database $db) {}

    /** @return ShippingMethodDTO[] */
    public function getAll(): array
    {
        $rows = $this->db->pdo()->query('SELECT * FROM shipping_methods ORDER BY price')->fetchAll();
        return array_map([$this, 'map'], $rows);
    }

    public function getById(int $id): ?ShippingMethodDTO
    {
        $stmt = $this->db->pdo()->prepare('SELECT * FROM shipping_methods WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->map($row) : null;
    }

    private function map(array $r): ShippingMethodDTO
    {
        return new ShippingMethodDTO(
            (int)$r['id'], (string)$r['name'],
            (float)$r['price'], (string)($r['delivery_days'] ?? '')
        );
    }
}
