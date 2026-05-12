<?php
declare(strict_types=1);

final class Cart
{
    private const SESSION_KEY = 'cart';

    public function __construct()
    {
        if (!isset($_SESSION[self::SESSION_KEY]) || !is_array($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = [];
        }
    }

    private function key(int $productId, string $variant = ''): string
    {
        return $productId . '|' . $variant;
    }

    public function add(
        int $productId,
        string $productName,
        float $unitPrice,
        ?string $image = null,
        string $variant = '',
        int $quantity = 1
    ): void {
        $k = $this->key($productId, $variant);
        if (isset($_SESSION[self::SESSION_KEY][$k])) {
            $_SESSION[self::SESSION_KEY][$k]['quantity'] += $quantity;
        } else {
            $_SESSION[self::SESSION_KEY][$k] = [
                'productId'   => $productId,
                'productName' => $productName,
                'unitPrice'   => $unitPrice,
                'image'       => $image,
                'variant'     => $variant,
                'quantity'    => $quantity,
            ];
        }
    }

    public function updateQuantity(int $productId, int $quantity, string $variant = ''): void
    {
        $k = $this->key($productId, $variant);
        if (!isset($_SESSION[self::SESSION_KEY][$k])) {
            return;
        }
        if ($quantity <= 0) {
            unset($_SESSION[self::SESSION_KEY][$k]);
        } else {
            $_SESSION[self::SESSION_KEY][$k]['quantity'] = $quantity;
        }
    }

    public function remove(int $productId, string $variant = ''): void
    {
        unset($_SESSION[self::SESSION_KEY][$this->key($productId, $variant)]);
    }

    /** @return CartItemDTO[] */
    public function getItems(): array
    {
        $items = [];
        foreach ($_SESSION[self::SESSION_KEY] as $row) {
            $items[] = new CartItemDTO(
                (int)$row['productId'],
                (string)$row['productName'],
                (float)$row['unitPrice'],
                (int)$row['quantity'],
                (string)$row['variant'],
                $row['image'] ?? null
            );
        }
        return $items;
    }

    public function getTotalPrice(): float
    {
        $total = 0.0;
        foreach ($_SESSION[self::SESSION_KEY] as $row) {
            $total += (float)$row['unitPrice'] * (int)$row['quantity'];
        }
        return $total;
    }

    public function getTotalQuantity(): int
    {
        $count = 0;
        foreach ($_SESSION[self::SESSION_KEY] as $row) {
            $count += (int)$row['quantity'];
        }
        return $count;
    }

    public function isEmpty(): bool
    {
        return $_SESSION[self::SESSION_KEY] === [];
    }

    public function clear(): void
    {
        $_SESSION[self::SESSION_KEY] = [];
    }
}
