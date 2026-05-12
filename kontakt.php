<?php
declare(strict_types=1);
require_once __DIR__ . '/src/bootstrap.php';

$pageTitle = 'Kontakt';
$values = ['name' => '', 'email' => '', 'message' => ''];
$v = new Validator();
$success = $_SESSION['contact_success'] ?? false;
unset($_SESSION['contact_success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($values as $k => $_) {
        $values[$k] = trim((string)($_POST[$k] ?? ''));
    }

    $v->required('name',    $values['name'],    'Zadejte své jméno.')
      ->minLength('name',   $values['name'], 2, 'Jméno musí mít alespoň 2 znaky.')
      ->required('email',   $values['email'],   'Zadejte e-mail.')
      ->email   ('email',   $values['email'],   'Neplatný formát e-mailu.')
      ->required('message', $values['message'], 'Zadejte zprávu.')
      ->minLength('message', $values['message'], 5, 'Zpráva musí mít alespoň 5 znaků.');

    if ($v->isValid()) {
        $_SESSION['contact_success'] = true;
        header('Location: kontakt.php');
        exit;
    }
}

require __DIR__ . '/partials/header.php';
?>
<h1 style="font-size:50px; text-align:center; background:#111; border-radius:10px">Kontakt</h1>
<div class="card">
  <p>Email: <strong>info@drumshop.com</strong></p>
  <p>Telefon: <strong>+420 723 392 453</strong></p>

  <?php if ($success): ?>
    <div class="flash" style="background:#264a26; padding:0.6rem; border-radius:6px; margin:0.5rem 0">
      Zpráva byla odeslána. Děkujeme!
    </div>
  <?php endif; ?>

  <h4>Máte otázky? Pošlete nám zprávu.</h4>
  <form method="post" class="input-row">
    <label>Jméno:<br>
      <input name="name" value="<?= e($values['name']) ?>"
             class="<?= $v->hasError('name') ? 'input--error' : '' ?>">
      <?php if ($v->hasError('name')): ?>
        <span class="error" style="color:#ff7777; display:block"><?= e($v->getError('name')) ?></span>
      <?php endif; ?>
    </label>
    <label>E-mail:<br>
      <input type="email" name="email" value="<?= e($values['email']) ?>"
             class="<?= $v->hasError('email') ? 'input--error' : '' ?>">
      <?php if ($v->hasError('email')): ?>
        <span class="error" style="color:#ff7777; display:block"><?= e($v->getError('email')) ?></span>
      <?php endif; ?>
    </label>
    <label>Zpráva:<br>
      <textarea name="message" class="<?= $v->hasError('message') ? 'input--error' : '' ?>"><?= e($values['message']) ?></textarea>
      <?php if ($v->hasError('message')): ?>
        <span class="error" style="color:#ff7777; display:block"><?= e($v->getError('message')) ?></span>
      <?php endif; ?>
    </label>
    <div><button class="btn btn-primary" type="submit">Odeslat</button></div>
  </form>
</div>
<?php require __DIR__ . '/partials/footer.php'; ?>
