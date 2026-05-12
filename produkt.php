<?php
declare(strict_types=1);
require_once __DIR__ . '/src/bootstrap.php';

$slug = trim((string)($_GET['slug'] ?? ''));
if ($slug === '') {
    header('Location: 404.php');
    exit;
}
$product = $productRepo->getBySlug($slug);
if ($product === null) {
    header('Location: 404.php');
    exit;
}
$images = $productRepo->getImages($product->id);
$params = $productRepo->getParameters($product->id);
$selectableParams = array_filter($params, fn(ProductParameterDTO $p) => $p->isSelectable());
$infoParams = array_filter($params, fn(ProductParameterDTO $p) => !$p->isSelectable());

// Seskupit selectable po parametru
$selectGroups = [];
foreach ($selectableParams as $p) {
    $selectGroups[$p->name][] = $p->value;
}

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// POST – přidání do košíku
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $variantParts = [];
    foreach (array_keys($selectGroups) as $name) {
        $val = trim((string)($_POST['variant'][$name] ?? ''));
        if ($val !== '') {
            $variantParts[] = $name . ': ' . $val;
        }
    }
    $variant = implode(', ', $variantParts);
    $quantity = max(1, (int)($_POST['quantity'] ?? 1));
    $cart->add(
        productId: $product->id,
        productName: $product->name,
        unitPrice: $product->price,
        image: $product->image,
        variant: $variant,
        quantity: $quantity,
    );
    $_SESSION['flash'] = 'Produkt byl přidán do košíku.';
    header('Location: produkt.php?slug=' . urlencode($product->slug));
    exit;
}

$pageTitle = $product->name;
require __DIR__ . '/partials/header.php';
?>
<article class="product-detail card">
  <div style="display:flex; gap:2rem; flex-wrap:wrap">
    <div style="flex:1; min-width:280px">
      <?php if ($product->image): ?>
        <img src="<?= e($product->image) ?>" alt="<?= e($product->name) ?>" style="width:100%; border-radius:8px">
      <?php endif; ?>
      <?php if ($images): ?>
        <div style="display:flex; gap:0.5rem; margin-top:0.5rem; flex-wrap:wrap">
          <?php foreach ($images as $img): ?>
            <img src="<?= e($img->url) ?>" alt="<?= e($img->alt ?? $product->name) ?>"
                 style="width:80px; height:80px; object-fit:cover; border-radius:6px">
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
    <div style="flex:1; min-width:280px">
      <h1><?= e($product->name) ?></h1>
      <div class="price" style="font-size:1.6rem; margin:1rem 0">
        <?php if ($product->isOnSale()): ?>
          <span style="text-decoration:line-through; opacity:0.6; margin-right:8px"><?= e(priceFmt((float)$product->oldPrice)) ?></span>
        <?php endif; ?>
        <strong><?= e(priceFmt($product->price)) ?></strong>
      </div>
      <?php if ($product->description): ?>
        <p><?= nl2br(e($product->description)) ?></p>
      <?php endif; ?>

      <?php if ($flash): ?>
        <div class="flash" style="background:#264a26; padding:0.6rem; border-radius:6px; margin:0.5rem 0">
          <?= e($flash) ?>
        </div>
      <?php endif; ?>

      <form method="post" class="input-row">
        <?php foreach ($selectGroups as $name => $values): ?>
          <label><?= e($name) ?>:
            <select name="variant[<?= e($name) ?>]" required>
              <?php foreach ($values as $v): ?>
                <option value="<?= e($v) ?>"><?= e($v) ?></option>
              <?php endforeach; ?>
            </select>
          </label>
        <?php endforeach; ?>
        <label>Množství:
          <input type="number" name="quantity" value="1" min="1" max="99">
        </label>
        <div style="margin-top:1rem">
          <button class="btn btn-primary" type="submit">Přidat do košíku</button>
        </div>
      </form>

      <?php if ($infoParams): ?>
        <h3 style="margin-top:1.5rem">Parametry</h3>
        <table style="width:100%; border-collapse:collapse">
          <?php foreach ($infoParams as $ip): ?>
            <tr>
              <th style="text-align:left; padding:0.3rem; border-bottom:1px solid #333"><?= e($ip->name) ?></th>
              <td style="padding:0.3rem; border-bottom:1px solid #333"><?= e($ip->value) ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
      <?php endif; ?>
    </div>
  </div>
</article>
<?php require __DIR__ . '/partials/footer.php'; ?>
