<?php
declare(strict_types=1);

final class OrderRepository
{
    public function __construct(private Database $db) {}

    /**
     * @param CartItemDTO[] $cartItems
     */
    public function create(
        int $customerId,
        int $shippingMethodId,
        int $paymentMethodId,
        ?string $note,
        array $cartItems
    ): OrderDTO {
        $shipRepo = new ShippingMethodRepository($this->db);
        $payRepo  = new PaymentMethodRepository($this->db);
        $shipping = $shipRepo->getById($shippingMethodId);
        $payment  = $payRepo->getById($paymentMethodId);
        $shippingPrice = $shipping?->price ?? 0.0;
        $paymentFee    = $payment?->fee ?? 0.0;

        $itemsTotal = 0.0;
        foreach ($cartItems as $i) {
            $itemsTotal += $i->getSubtotal();
        }
        $total = $itemsTotal + $shippingPrice + $paymentFee;

        $pdo = $this->db->pdo();
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare('
                INSERT INTO orders (customer_id, shipping_method_id, payment_method_id,
                                    shipping_price, payment_fee, total_price, note, status, created_at)
                VALUES (:cid, :smid, :pmid, :sp, :pf, :tp, :nt, :st, :ca)
            ');
            $stmt->execute([
                ':cid' => $customerId, ':smid' => $shippingMethodId, ':pmid' => $paymentMethodId,
                ':sp' => $shippingPrice, ':pf' => $paymentFee, ':tp' => $total,
                ':nt' => $note, ':st' => 'new', ':ca' => date('Y-m-d H:i:s'),
            ]);
            $orderId = (int)$pdo->lastInsertId();

            $itemStmt = $pdo->prepare('
                INSERT INTO order_items (order_id, product_id, product_name, variant, quantity, unit_price)
                VALUES (:oid, :pid, :pn, :var, :qty, :up)
            ');
            foreach ($cartItems as $i) {
                $itemStmt->execute([
                    ':oid' => $orderId, ':pid' => $i->productId,
                    ':pn' => $i->productName, ':var' => $i->variant,
                    ':qty' => $i->quantity, ':up' => $i->unitPrice,
                ]);
            }

            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }

        return new OrderDTO($orderId, $customerId, $total);
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->pdo()->prepare('SELECT * FROM orders WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** @return array<int,array<string,mixed>> */
    public function getItems(int $orderId): array
    {
        $stmt = $this->db->pdo()->prepare('SELECT * FROM order_items WHERE order_id = :id');
        $stmt->execute([':id' => $orderId]);
        return $stmt->fetchAll();
    }
}
