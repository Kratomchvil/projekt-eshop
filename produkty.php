<?php
declare(strict_types=1);
require_once __DIR__ . '/src/bootstrap.php';

$slug = trim((string)($_GET['slug'] ?? ''));
$category = null;

if ($slug !== '') {
    $category = $categoryRepo->getBySlug($slug);
    if ($category === null) {
        header('Location: 404.php');
        exit;
    }
    $products = $productRepo->getByCategorySlug($slug);
    $pageTitle = $category->name;
} else {
    // Bez slugu = všechny produkty
    $stmt = $db->pdo()->query('SELECT * FROM products ORDER BY name');
    $products = [];
    foreach ($stmt->fetchAll() as $r) {
        $p = new ProductDTO(
            (int)$r['id'], (string)$r['name'], (string)$r['slug'], (float)$r['price'],
            $r['old_price'] !== null ? (float)$r['old_price'] : null,
            $r['description'] ?? null, $r['image'] ?? null,
            (int)$r['category_id'], (bool)$r['featured']
        );
        $products[] = $p;
    }
    $pageTitle = 'Všechny produkty';
}

require __DIR__ . '/partials/header.php';
?>
<h1 style="font-size: 40px; text-align: center; background-color: #111; border-radius: 10px;">
  <?= e($pageTitle) ?>
</h1>
<?php if ($category && $category->description): ?>
  <p class="muted" style="text-align:center"><?= e($category->description) ?></p>
<?php endif; ?>
<?php if ($products === []): ?>
  <p>V této kategorii zatím nejsou žádné produkty.</p>
<?php else: ?>
  <div class="product-list">
    <?php foreach ($products as $product):
        $product->hasVariants = false;
        // vyhneme se dotazu pro každý produkt – v product-card je jen text štítku
        require __DIR__ . '/partials/product-card.php';
    endforeach; ?>
  </div>
<?php endif; ?>
<?php require __DIR__ . '/partials/footer.php'; ?>
