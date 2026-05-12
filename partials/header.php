<?php
/** @var string|null $pageTitle */
declare(strict_types=1);
if (!isset($cart)) { $cart = new Cart(); }
$pageTitle = $pageTitle ?? 'DrumShop';
$cartItemCount = $cart->getTotalQuantity();
?><!doctype html>
<html lang="cs">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= e($pageTitle) ?> | DrumShop</title>
  <link rel="stylesheet" href="assets/css/variables.css">
  <link rel="stylesheet" href="assets/css/base.css">
  <link rel="stylesheet" href="assets/css/main.css">
  <link rel="stylesheet" href="assets/css/responsive.css">
  <link rel="stylesheet" href="assets/css/php-extras.css">
  <link rel="icon" type="image/x-icon" href="assets/logo/logo-main.png">
</head>
<body>
  <header class="site-header">
    <div class="container wrap">
      <a href="kosik.php" class="mobile-cart cart-badge">
        <img src="assets/logo/shopping-cart.png" alt="Košík" style="height:20px">
        <span class="cart-count"><?= (int)$cartItemCount ?></span>
      </a>
      <div class="brand">
        <a href="index.php" class="logo">
          <img src="assets/logo/logo-main.png" alt="DrumShop logo" style="height:40px; display:block">
        </a>
        <span class="muted brand-text"><b>DrumShop</b> – bicí nástroje &amp; příslušenství</span>
      </div>
      <button class="hamburger" aria-controls="main-nav" aria-expanded="false"></button>
      <nav id="main-nav" class="site-nav">
        <a href="produkty.php">Produkty</a>
        <a href="kategorie.php">Kategorie</a>
        <a href="o-nas.php">O nás</a>
        <a href="kontakt.php">Kontakt</a>
        <form action="vyhledavani.php" method="get" class="header-search" style="display:inline-flex; gap:4px">
          <input type="search" name="q" placeholder="Hledat..." aria-label="Hledat produkty"
                 value="<?= e($_GET['q'] ?? '') ?>"
                 style="padding:0.4rem; border-radius:8px; border:1px solid #444; background:transparent; color:inherit">
        </form>
        <a href="kosik.php" class="desktop-cart btn-cart cart-badge">
          <img src="assets/logo/shopping-cart.png" alt="Košík" style="height:18px">
          <span class="muted" style="margin-left:6px">Košík</span>
          <span class="cart-count"><?= (int)$cartItemCount ?></span>
        </a>
      </nav>
      <div class="hamburger-menu" aria-hidden="true">
        <a href="o-nas.php">O nás</a>
        <a href="produkty.php">Produkty</a>
        <a href="kategorie.php">Kategorie</a>
        <a href="kontakt.php">Kontakt</a>
      </div>
    </div>
  </header>
  <main class="container">
