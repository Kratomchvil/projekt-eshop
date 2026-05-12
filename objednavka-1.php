<?php
declare(strict_types=1);
require_once __DIR__ . '/src/bootstrap.php';

if ($cart->isEmpty()) {
    header('Location: kosik.php');
    exit;
}

$pageTitle = 'Objednávka – krok 1';
$saved = $_SESSION['order']['customer'] ?? [];
$errors = [];

$values = [
    'first_name' => $saved['first_name'] ?? '',
    'last_name'  => $saved['last_name']  ?? '',
    'email'      => $saved['email']      ?? '',
    'phone'      => $saved['phone']      ?? '',
    'street'     => $saved['street']     ?? '',
    'city'       => $saved['city']       ?? '',
    'zip'        => $saved['zip']        ?? '',
    'note'       => $saved['note']       ?? '',
];

$v = new Validator();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($values as $k => $_) {
        $values[$k] = trim((string)($_POST[$k] ?? ''));
    }

    $v->required('first_name', $values['first_name'], 'Zadejte jméno.')
      ->required('last_name',  $values['last_name'],  'Zadejte příjmení.')
      ->required('email',      $values['email'],      'Zadejte e-mail.')
      ->email   ('email',      $values['email'],      'Neplatný formát e-mailu.')
      ->required('phone',      $values['phone'],      'Zadejte telefon.')
      ->required('street',     $values['street'],     'Zadejte ulici a č. p.')
      ->required('city',       $values['city'],       'Zadejte město.')
      ->required('zip',        $values['zip'],        'Zadejte PSČ.')
      ->pattern ('zip',        $values['zip'],        '/^\d{3}\s?\d{2}$/', 'PSČ musí mít 5 číslic.');

    if ($v->isValid()) {
        $_SESSION['order']['customer'] = $values;
        header('Location: objednavka-2.php');
        exit;
    }
    $errors = $v->getErrors();
}

require __DIR__ . '/partials/header.php';
?>
<h1 style="font-size:36px; text-align:center; background:#111; border-radius:10px">1. Dodací údaje</h1>
<form method="post" class="card input-row">
  <?php
  $field = function(string $name, string $label, string $type = 'text') use ($values, $v) {
      $err = $v->getError($name);
      $cls = $v->hasError($name) ? 'input--error' : '';
      echo '<label>' . e($label) . ':<br>';
      if ($type === 'textarea') {
          echo '<textarea name="' . e($name) . '" class="' . $cls . '">'
              . e((string)$values[$name]) . '</textarea>';
      } else {
          echo '<input type="' . e($type) . '" name="' . e($name)
              . '" value="' . e((string)$values[$name]) . '" class="' . $cls . '">';
      }
      if ($err) {
          echo '<span class="error" style="color:#ff7777; display:block">' . e($err) . '</span>';
      }
      echo '</label>';
  };
  $field('first_name', 'Jméno');
  $field('last_name',  'Příjmení');
  $field('email',      'E-mail', 'email');
  $field('phone',      'Telefon');
  $field('street',     'Ulice a č. p.');
  $field('city',       'Město');
  $field('zip',        'PSČ');
  $field('note',       'Poznámka', 'textarea');
  ?>
  <div style="margin-top:1rem; display:flex; gap:1rem; justify-content:space-between">
    <a class="btn btn-ghost" href="kosik.php">Zpět do košíku</a>
    <button class="btn btn-primary" type="submit">Pokračovat na dopravu</button>
  </div>
</form>
<?php require __DIR__ . '/partials/footer.php'; ?>
