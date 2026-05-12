<?php
declare(strict_types=1);

final class PaymentMethodRepository
{
    public function __construct(private Database $db) {}

    /** @return PaymentMethodDTO[] */
    public function getAll(): array
    {
        $rows = $this->db->pdo()->query('SELECT * FROM payment_methods ORDER BY id')->fetchAll();
        return array_map([$this, 'map'], $rows);
    }

    public function getById(int $id): ?PaymentMethodDTO
    {
        $stmt = $this->db->pdo()->prepare('SELECT * FROM payment_methods WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->map($row) : null;
    }

    private function map(array $r): PaymentMethodDTO
    {
        return new PaymentMethodDTO((int)$r['id'], (string)$r['name'], (float)$r['fee']);
    }
}
