<?php
declare(strict_types=1);
require_once __DIR__ . '/src/bootstrap.php';

$pageTitle = 'Kategorie';
$categories = $categoryRepo->getAll();

require __DIR__ . '/partials/header.php';
?>
<h1 style="font-size: 50px; text-align: center; background-color: #111; border-radius: 10px;">Kategorie produktů</h1>
<div class="product-list">
  <?php foreach ($categories as $c): ?>
    <?php $count = $categoryRepo->getProductCount($c->id); ?>
    <div class="card product-card">
      <a class="thumb" href="produkty.php?slug=<?= e($c->slug) ?>">
        <?php if ($c->image): ?><img src="<?= e($c->image) ?>" alt="<?= e($c->name) ?>"><?php endif; ?>
      </a>
      <h3 class="title"><?= e($c->name) ?> <span class="muted">(<?= (int)$count ?>)</span></h3>
      <?php if ($c->description): ?><div class="muted"><?= e($c->description) ?></div><?php endif; ?>
      <a href="produkty.php?slug=<?= e($c->slug) ?>" class="btn btn-primary">Zobrazit produkty</a>
    </div>
  <?php endforeach; ?>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
