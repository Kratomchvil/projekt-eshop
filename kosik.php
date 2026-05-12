<?php
declare(strict_types=1);
require_once __DIR__ . '/src/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $productId = (int)($_POST['product_id'] ?? 0);
    $variant = (string)($_POST['variant'] ?? '');

    if ($action === 'update') {
        $qty = (int)($_POST['quantity'] ?? 1);
        $cart->updateQuantity($productId, $qty, $variant);
    } elseif ($action === 'remove') {
        $cart->remove($productId, $variant);
    }
    header('Location: kosik.php');
    exit;
}

$items = $cart->getItems();
$pageTitle = 'Košík';
require __DIR__ . '/partials/header.php';
?>
<h1 style="font-size: 40px; text-align: center; background-color: #111; border-radius: 10px;">Váš košík</h1>

<?php if ($cart->isEmpty()): ?>
  <div class="card">
    <p>Váš košík je prázdný.</p>
    <p><a class="btn btn-primary" href="produkty.php">Pokračovat v nákupu</a></p>
  </div>
<?php else: ?>
  <div class="card">
    <table style="width:100%; border-collapse:collapse">
      <thead>
        <tr>
          <th style="text-align:left; padding:0.5rem">Produkt</th>
          <th style="text-align:left; padding:0.5rem">Varianta</th>
          <th style="padding:0.5rem">Cena</th>
          <th style="padding:0.5rem">Množství</th>
          <th style="padding:0.5rem">Mezisoučet</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $item): ?>
          <tr style="border-top:1px solid #333">
            <td style="padding:0.5rem">
              <?php if ($item->image): ?>
                <img src="<?= e($item->image) ?>" alt="" style="width:50px; height:50px; object-fit:cover; border-radius:4px; vertical-align:middle; margin-right:8px">
              <?php endif; ?>
              <?= e($item->productName) ?>
            </td>
            <td style="padding:0.5rem"><?= e($item->variant) ?: '<span class="muted">—</span>' ?></td>
            <td style="padding:0.5rem; text-align:right"><?= e(priceFmt($item->unitPrice)) ?></td>
            <td style="padding:0.5rem; text-align:center">
              <form method="post" style="display:inline-flex; gap:4px; align-items:center">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="product_id" value="<?= (int)$item->productId ?>">
                <input type="hidden" name="variant" value="<?= e($item->variant) ?>">
                <input type="number" name="quantity" min="1" max="99"
                       value="<?= (int)$item->quantity ?>" style="width:60px">
                <button class="btn btn-ghost" type="submit">Změnit</button>
              </form>
            </td>
            <td style="padding:0.5rem; text-align:right"><?= e(priceFmt($item->getSubtotal())) ?></td>
            <td style="padding:0.5rem">
              <form method="post" style="display:inline">
                <input type="hidden" name="action" value="remove">
                <input type="hidden" name="product_id" value="<?= (int)$item->productId ?>">
                <input type="hidden" name="variant" value="<?= e($item->variant) ?>">
                <button class="btn btn-ghost" type="submit">Odebrat</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="4" style="padding:0.5rem; text-align:right"><strong>Mezisoučet:</strong></td>
          <td style="padding:0.5rem; text-align:right"><strong><?= e(priceFmt($cart->getTotalPrice())) ?></strong></td>
          <td></td>
        </tr>
      </tfoot>
    </table>
  </div>
  <div style="margin-top:1rem; display:flex; gap:1rem; justify-content:space-between; flex-wrap:wrap">
    <a class="btn btn-ghost" href="produkty.php">Pokračovat v nákupu</a>
    <a class="btn btn-primary" href="objednavka-1.php">Pokračovat k objednávce</a>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/partials/footer.php'; ?>
