<?php
declare(strict_types=1);
require_once __DIR__ . '/src/bootstrap.php';

$orderId = (int)($_SESSION['last_order_id'] ?? 0);
if ($orderId <= 0) {
    header('Location: index.php');
    exit;
}
$order = $orderRepo->getById($orderId);
$items = $orderRepo->getItems($orderId);

$pageTitle = 'Objednávka odeslána';
require __DIR__ . '/partials/header.php';
?>
<div class="card" style="text-align:center">
  <h1>Děkujeme za objednávku!</h1>
  <p>Vaše objednávka <strong>č. <?= (int)$orderId ?></strong> byla úspěšně přijata.</p>
  <?php if ($order): ?>
    <p>Celková cena: <strong><?= e(priceFmt((float)$order['total_price'])) ?></strong></p>
  <?php endif; ?>

  <?php if ($items): ?>
    <h3>Souhrn položek</h3>
    <table style="width:100%; border-collapse:collapse; max-width:600px; margin:0 auto">
      <?php foreach ($items as $i): ?>
        <tr style="border-top:1px solid #333">
          <td style="padding:0.4rem; text-align:left"><?= e($i['product_name']) ?>
            <?php if (!empty($i['variant'])): ?><span class="muted">(<?= e($i['variant']) ?>)</span><?php endif; ?>
          </td>
          <td style="padding:0.4rem"><?= (int)$i['quantity'] ?>×</td>
          <td style="padding:0.4rem; text-align:right"><?= e(priceFmt((float)$i['unit_price'] * (int)$i['quantity'])) ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>

  <p style="margin-top:1.5rem">
    <a class="btn btn-primary" href="index.php">Zpět na hlavní stránku</a>
  </p>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
