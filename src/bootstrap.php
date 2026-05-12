<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// DTO
require_once __DIR__ . '/DTO/CategoryDTO.php';
require_once __DIR__ . '/DTO/ProductDTO.php';
require_once __DIR__ . '/DTO/ProductImageDTO.php';
require_once __DIR__ . '/DTO/ProductParameterDTO.php';
require_once __DIR__ . '/DTO/ShippingMethodDTO.php';
require_once __DIR__ . '/DTO/PaymentMethodDTO.php';
require_once __DIR__ . '/DTO/CustomerDTO.php';
require_once __DIR__ . '/DTO/OrderDTO.php';
require_once __DIR__ . '/DTO/CartItemDTO.php';

// Core
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Cart.php';
require_once __DIR__ . '/Validator.php';

// Repositories
require_once __DIR__ . '/Repository/CategoryRepository.php';
require_once __DIR__ . '/Repository/ProductRepository.php';
require_once __DIR__ . '/Repository/ShippingMethodRepository.php';
require_once __DIR__ . '/Repository/PaymentMethodRepository.php';
require_once __DIR__ . '/Repository/CustomerRepository.php';
require_once __DIR__ . '/Repository/OrderRepository.php';

// Globální instance pro stránky
$db = Database::getInstance();
$categoryRepo = new CategoryRepository($db);
$productRepo  = new ProductRepository($db);
$shippingRepo = new ShippingMethodRepository($db);
$paymentRepo  = new PaymentMethodRepository($db);
$customerRepo = new CustomerRepository($db);
$orderRepo    = new OrderRepository($db);
$cart         = new Cart();

/** Bezpečný výpis */
function e(?string $s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Formátování ceny */
function priceFmt(float $price): string {
    return number_format($price, 0, ',', ' ') . ' Kč';
}
