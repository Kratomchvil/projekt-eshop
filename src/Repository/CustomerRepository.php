<?php
declare(strict_types=1);

final class CustomerRepository
{
    public function __construct(private Database $db) {}

    public function create(
        string $firstName, string $lastName, string $email, string $phone,
        string $street, string $city, string $zip
    ): CustomerDTO {
        $stmt = $this->db->pdo()->prepare('
            INSERT INTO customers (first_name, last_name, email, phone, street, city, zip)
            VALUES (:fn, :ln, :em, :ph, :st, :ci, :zi)
        ');
        $stmt->execute([
            ':fn' => $firstName, ':ln' => $lastName, ':em' => $email,
            ':ph' => $phone, ':st' => $street, ':ci' => $city, ':zi' => $zip,
        ]);
        $id = (int)$this->db->pdo()->lastInsertId();
        return new CustomerDTO($id, $firstName, $lastName, $email, $phone, $street, $city, $zip);
    }
}
