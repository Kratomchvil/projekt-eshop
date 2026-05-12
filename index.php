<?php
declare(strict_types=1);
require_once __DIR__ . '/src/bootstrap.php';

$pageTitle = 'Hlavní stránka';
$featured = $productRepo->getFeatured(6);
$categories = $categoryRepo->getAll();

require __DIR__ . '/partials/header.php';
?>
<section class="hero">
  <div class="left">
    <h1>Vyberte si svůj nový bicí set</h1>
    <p class="muted">Široká nabídka pro začátečníky i profesionály — sety, činely, paličky a více.</p>
    <p><a href="produkty.php" class="btn btn-primary">Prohlédnout produkty</a></p>
  </div>
  <div class="right">
    <img src="https://i.pinimg.com/originals/ea/78/84/ea7884e6a9839c5d51155cc7d432753e.jpg" alt="Drum set">
  </div>
</section>

<section class="featured">
  <h2 style="font-size: 55px; text-align: center; background-color: #111; border-radius: 10px;">Nejprodávanější produkty</h2>
  <div class="product-list">
    <?php foreach ($featured as $product): ?>
      <?php require __DIR__ . '/partials/product-card.php'; ?>
    <?php endforeach; ?>
  </div>
</section>

<section class="featured" style="margin-top:2rem">
  <h2 style="font-size: 40px; text-align: center; background-color: #111; border-radius: 10px;">Kategorie</h2>
  <div class="product-list">
    <?php foreach ($categories as $c): ?>
      <div class="card product-card">
        <a class="thumb" href="produkty.php?slug=<?= e($c->slug) ?>">
          <?php if ($c->image): ?><img src="<?= e($c->image) ?>" alt="<?= e($c->name) ?>"><?php endif; ?>
        </a>
        <h3 class="title"><?= e($c->name) ?></h3>
        <?php if ($c->description): ?><div class="muted"><?= e($c->description) ?></div><?php endif; ?>
        <a href="produkty.php?slug=<?= e($c->slug) ?>" class="btn btn-primary">Zobrazit</a>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<?php require __DIR__ . '/partials/footer.php'; ?>
