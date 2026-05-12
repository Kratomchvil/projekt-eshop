<?php
declare(strict_types=1);
require_once __DIR__ . '/src/bootstrap.php';

http_response_code(404);
$pageTitle = '404 – Stránka nenalezena';
require __DIR__ . '/partials/header.php';
?>
<div class="card" style="text-align:center">
  <h1 style="font-size:64px">404</h1>
  <p>Bohužel jsme nenašli, co hledáte. Stránka, produkt ani kategorie neexistuje.</p>
  <p><a class="btn btn-primary" href="index.php">Zpět na hlavní stránku</a></p>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
