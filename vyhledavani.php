<?php
declare(strict_types=1);
require_once __DIR__ . '/src/bootstrap.php';

$query = trim((string)($_GET['q'] ?? ''));
$results = [];
if ($query !== '') {
    $results = $productRepo->search($query);
}
$pageTitle = 'Vyhledávání';
require __DIR__ . '/partials/header.php';
?>
<h1 style="font-size:36px; text-align:center; background:#111; border-radius:10px">Vyhledávání</h1>
<form method="get" class="card input-row">
  <label>Hledaný výraz:
    <input type="search" name="q" value="<?= e($query) ?>" required>
  </label>
  <div><button class="btn btn-primary" type="submit">Hledat</button></div>
</form>

<?php if ($query === ''): ?>
  <p>Zadejte hledaný výraz.</p>
<?php elseif ($results === []): ?>
  <p>Pro výraz „<strong><?= e($query) ?></strong>“ jsme nenašli žádné produkty.</p>
<?php else: ?>
  <p>Nalezeno <?= count($results) ?> produktů pro „<strong><?= e($query) ?></strong>“:</p>
  <div class="product-list">
    <?php foreach ($results as $product):
        require __DIR__ . '/partials/product-card.php';
    endforeach; ?>
  </div>
<?php endif; ?>
<?php require __DIR__ . '/partials/footer.php'; ?>
