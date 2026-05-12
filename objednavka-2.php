<?php
declare(strict_types=1);
require_once __DIR__ . '/src/bootstrap.php';

if ($cart->isEmpty()) {
    header('Location: kosik.php');
    exit;
}
if (empty($_SESSION['order']['customer'])) {
    header('Location: objednavka-1.php');
    exit;
}

$shippingMethods = $shippingRepo->getAll();
$paymentMethods  = $paymentRepo->getAll();

$selectedShipping = (int)($_SESSION['order']['shipping_id'] ?? 0);
$selectedPayment  = (int)($_SESSION['order']['payment_id'] ?? 0);

$v = new Validator();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedShipping = (int)($_POST['shipping_id'] ?? 0);
    $selectedPayment  = (int)($_POST['payment_id']  ?? 0);

    $v->in('shipping_id', $selectedShipping, array_map(fn($s) => $s->id, $shippingMethods), 'Vyberte způsob dopravy.')
      ->in('payment_id',  $selectedPayment,  array_map(fn($p) => $p->id, $paymentMethods),  'Vyberte způsob platby.');

    if ($v->isValid()) {
        $_SESSION['order']['shipping_id'] = $selectedShipping;
        $_SESSION['order']['payment_id']  = $selectedPayment;
        header('Location: objednavka-3.php');
        exit;
    }
}

$pageTitle = 'Objednávka – krok 2';
require __DIR__ . '/partials/header.php';
?>
<h1 style="font-size:36px; text-align:center; background:#111; border-radius:10px">2. Doprava a platba</h1>
<form method="post" class="card input-row">
  <h3>Doprava</h3>
  <?php if ($v->hasError('shipping_id')): ?>
    <span class="error" style="color:#ff7777"><?= e($v->getError('shipping_id')) ?></span>
  <?php endif; ?>
  <?php foreach ($shippingMethods as $s): ?>
    <label style="display:block; padding:0.4rem 0">
      <input type="radio" name="shipping_id" value="<?= (int)$s->id ?>"
        <?= $selectedShipping === $s->id ? 'checked' : '' ?>>
      <strong><?= e($s->name) ?></strong> –
      <?= $s->isFree() ? 'Zdarma' : e(priceFmt($s->price)) ?>
      <span class="muted">(<?= e($s->deliveryDays) ?>)</span>
    </label>
  <?php endforeach; ?>

  <h3 style="margin-top:1rem">Platba</h3>
  <?php if ($v->hasError('payment_id')): ?>
    <span class="error" style="color:#ff7777"><?= e($v->getError('payment_id')) ?></span>
  <?php endif; ?>
  <?php foreach ($paymentMethods as $p): ?>
    <label style="display:block; padding:0.4rem 0">
      <input type="radio" name="payment_id" value="<?= (int)$p->id ?>"
        <?= $selectedPayment === $p->id ? 'checked' : '' ?>>
      <strong><?= e($p->name) ?></strong>
      <?= $p->fee > 0 ? ' – poplatek ' . e(priceFmt($p->fee)) : '' ?>
    </label>
  <?php endforeach; ?>

  <div style="margin-top:1rem; display:flex; gap:1rem; justify-content:space-between">
    <a class="btn btn-ghost" href="objednavka-1.php">Zpět</a>
    <button class="btn btn-primary" type="submit">Pokračovat na shrnutí</button>
  </div>
</form>
<?php require __DIR__ . '/partials/footer.php'; ?>
