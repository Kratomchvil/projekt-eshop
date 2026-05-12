<?php
declare(strict_types=1);
/** @var ProductDTO $product */
?>
<div class="card product-card">
  <a class="thumb" href="produkt.php?slug=<?= e($product->slug) ?>">
    <?php if ($product->image): ?>
      <img src="<?= e($product->image) ?>" alt="<?= e($product->name) ?>">
    <?php endif; ?>
  </a>
  <h3 class="title"><?= e($product->name) ?></h3>
  <?php if ($product->description): ?>
    <div class="muted"><?= e(mb_substr($product->description, 0, 80)) ?><?= mb_strlen($product->description) > 80 ? '…' : '' ?></div>
  <?php endif; ?>
  <div class="price">
    <?php if ($product->isOnSale()): ?>
      <span style="text-decoration:line-through; opacity:0.6; margin-right:6px"><?= e(priceFmt((float)$product->oldPrice)) ?></span>
    <?php endif; ?>
    <?= e(priceFmt($product->price)) ?>
  </div>
  <a href="produkt.php?slug=<?= e($product->slug) ?>" class="btn btn-primary">
    <?= $product->hasVariants ? 'Vybrat variantu' : 'Zobrazit' ?>
  </a>
</div>
