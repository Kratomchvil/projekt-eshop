<?php
declare(strict_types=1);
require_once __DIR__ . '/src/bootstrap.php';

if ($cart->isEmpty()) { header('Location: kosik.php'); exit; }
if (empty($_SESSION['order']['customer']))    { header('Location: objednavka-1.php'); exit; }
if (empty($_SESSION['order']['shipping_id'])) { header('Location: objednavka-2.php'); exit; }

$customer = $_SESSION['order']['customer'];
$shipping = $shippingRepo->getById((int)$_SESSION['order']['shipping_id']);
$payment  = $paymentRepo->getById((int)$_SESSION['order']['payment_id']);
$items    = $cart->getItems();

if (!$shipping || !$payment) {
    header('Location: objednavka-2.php');
    exit;
}

$itemsTotal = $cart->getTotalPrice();
$total = $itemsTotal + $shipping->price + $payment->fee;

$v = new Validator();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $agree = $_POST['agree'] ?? '';
    $v->required('agree', $agree, 'Musíte souhlasit s obchodními podmínkami.');

    if ($v->isValid()) {
        // Ulož zákazníka a objednávku
        $newCustomer = $customerRepo->create(
            firstName: $customer['first_name'],
            lastName:  $customer['last_name'],
            email:     $customer['email'],
            phone:     $customer['phone'],
            street:    $customer['street'],
            city:      $customer['city'],
            zip:       $customer['zip'],
        );
        $order = $orderRepo->create(
            customerId: $newCustomer->id,
            shippingMethodId: $shipping->id,
            paymentMethodId: $payment->id,
            note: $customer['note'] ?? null,
            cartItems: $items,
        );

        // Vyprázdnit košík + uložit ID pro potvrzovací stránku
        $cart->clear();
        $_SESSION['last_order_id'] = $order->id;
        unset($_SESSION['order']);

        header('Location: objednavka-potvrzeni.php');
        exit;
    }
}

$pageTitle = 'Objednávka – shrnutí';
require __DIR__ . '/partials/header.php';
?>
<h1 style="font-size:36px; text-align:center; background:#111; border-radius:10px">3. Shrnutí</h1>

<div class="card">
  <h3>Dodací údaje</h3>
  <p>
    <?= e($customer['first_name'] . ' ' . $customer['last_name']) ?><br>
    <?= e($customer['street']) ?>, <?= e($customer['zip']) ?> <?= e($customer['city']) ?><br>
    <?= e($customer['email']) ?> | <?= e($customer['phone']) ?>
  </p>
  <?php if (!empty($customer['note'])): ?>
    <p><strong>Poznámka:</strong> <?= e($customer['note']) ?></p>
  <?php endif; ?>

  <h3>Doprava a platba</h3>
  <p>
    Doprava: <strong><?= e($shipping->name) ?></strong>
    (<?= $shipping->isFree() ? 'Zdarma' : e(priceFmt($shipping->price)) ?>)<br>
    Platba: <strong><?= e($payment->name) ?></strong>
    <?= $payment->fee > 0 ? '(poplatek ' . e(priceFmt($payment->fee)) . ')' : '' ?>
  </p>

  <h3>Položky</h3>
  <table style="width:100%; border-collapse:collapse">
    <?php foreach ($items as $item): ?>
      <tr style="border-top:1px solid #333">
        <td style="padding:0.4rem"><?= e($item->productName) ?>
          <?php if ($item->variant): ?><span class="muted">(<?= e($item->variant) ?>)</span><?php endif; ?>
        </td>
        <td style="padding:0.4rem; text-align:right"><?= (int)$item->quantity ?>×</td>
        <td style="padding:0.4rem; text-align:right"><?= e(priceFmt($item->unitPrice)) ?></td>
        <td style="padding:0.4rem; text-align:right"><?= e(priceFmt($item->getSubtotal())) ?></td>
      </tr>
    <?php endforeach; ?>
  </table>

  <hr>
  <p>Cena zboží: <strong><?= e(priceFmt($itemsTotal)) ?></strong></p>
  <p>Doprava: <strong><?= e(priceFmt($shipping->price)) ?></strong></p>
  <p>Poplatek za platbu: <strong><?= e(priceFmt($payment->fee)) ?></strong></p>
  <p style="font-size:1.4rem">Celkem k úhradě: <strong><?= e(priceFmt($total)) ?></strong></p>

  <form method="post" style="margin-top:1rem">
    <label>
      <input type="checkbox" name="agree" value="1">
      Souhlasím s obchodními podmínkami.
    </label>
    <?php if ($v->hasError('agree')): ?>
      <div class="error" style="color:#ff7777"><?= e($v->getError('agree')) ?></div>
    <?php endif; ?>
    <div style="margin-top:1rem; display:flex; gap:1rem; justify-content:space-between">
      <a class="btn btn-ghost" href="objednavka-2.php">Zpět</a>
      <button class="btn btn-primary" type="submit">Odeslat objednávku</button>
    </div>
  </form>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
