<?php
declare(strict_types=1);

/**
 * Inicializace SQLite databáze – vytvoří schema a vloží vzorová data.
 * Spuštění: php database/init.php
 */

require_once __DIR__ . '/../src/bootstrap.php';

$dbPath = __DIR__ . '/eshop.db';
$pdo = Database::getInstance()->pdo();

// Vyčistit (drop tables)
$pdo->exec('PRAGMA foreign_keys = OFF');
foreach (['order_items','orders','customers','product_parameters','product_images',
          'products','categories','shipping_methods','payment_methods'] as $t) {
    $pdo->exec("DROP TABLE IF EXISTS {$t}");
}
$pdo->exec('PRAGMA foreign_keys = ON');

// Schema
$pdo->exec('
CREATE TABLE categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    slug TEXT NOT NULL UNIQUE,
    description TEXT,
    image TEXT
);

CREATE TABLE products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    slug TEXT NOT NULL UNIQUE,
    description TEXT,
    price REAL NOT NULL,
    old_price REAL,
    image TEXT,
    category_id INTEGER NOT NULL REFERENCES categories(id),
    featured INTEGER NOT NULL DEFAULT 0
);

CREATE TABLE product_images (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    url TEXT NOT NULL,
    alt TEXT
);

CREATE TABLE product_parameters (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    product_id INTEGER NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    name TEXT NOT NULL,
    value TEXT NOT NULL,
    type TEXT NOT NULL CHECK(type IN ("select","info"))
);

CREATE TABLE shipping_methods (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    price REAL NOT NULL,
    delivery_days TEXT NOT NULL
);

CREATE TABLE payment_methods (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    fee REAL NOT NULL DEFAULT 0
);

CREATE TABLE customers (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    email TEXT NOT NULL,
    phone TEXT NOT NULL,
    street TEXT NOT NULL,
    city TEXT NOT NULL,
    zip TEXT NOT NULL
);

CREATE TABLE orders (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    customer_id INTEGER NOT NULL REFERENCES customers(id),
    shipping_method_id INTEGER NOT NULL REFERENCES shipping_methods(id),
    payment_method_id INTEGER NOT NULL REFERENCES payment_methods(id),
    shipping_price REAL NOT NULL DEFAULT 0,
    payment_fee REAL NOT NULL DEFAULT 0,
    total_price REAL NOT NULL,
    note TEXT,
    status TEXT NOT NULL DEFAULT "new",
    created_at TEXT NOT NULL
);

CREATE TABLE order_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id INTEGER NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    product_id INTEGER NOT NULL,
    product_name TEXT NOT NULL,
    variant TEXT,
    quantity INTEGER NOT NULL,
    unit_price REAL NOT NULL
);
');

// Seed: kategorie
$cat = $pdo->prepare('INSERT INTO categories (name, slug, description, image) VALUES (:n, :s, :d, :i)');
$categories = [
    ['Bicí sety', 'bici-sety', 'Akustické i elektronické bicí sety pro každého.', 'https://www.drumcenter.cz/bin/goods_images/produkt_6/102066_img_67a62d6517b67_1.jpg'],
    ['Činely', 'cinely', 'Hi-haty, crash, ride a další.', 'https://www.muziker.cz/cdn/shop/files/sabian-aax-suite-set-cinelu-1.jpg'],
    ['Paličky', 'palicky', 'Paličky, košťata a metly.', 'https://www.muziker.cz/cdn/shop/files/vic-firth-american-classic-5a-pair.jpg'],
    ['Příslušenství', 'prislusenstvi', 'Stojany, blány, hardware.', 'https://www.muziker.cz/cdn/shop/files/tama-iron-cobra-200-single-pedal.jpg'],
];
foreach ($categories as $c) {
    $cat->execute([':n'=>$c[0], ':s'=>$c[1], ':d'=>$c[2], ':i'=>$c[3]]);
}

// Seed: produkty
$prod = $pdo->prepare('INSERT INTO products (name, slug, description, price, old_price, image, category_id, featured)
                       VALUES (:n,:s,:d,:p,:op,:i,:cid,:f)');
$products = [
    ['Standard Drum Set', 'standard-drum-set', '5-dílný akustický bicí set pro začátečníky i pokročilé. Obsahuje basový buben, malý buben, dva tomy a floor tom.', 12990, 14990, 'https://www.drumcenter.cz/bin/goods_images/produkt_6/102066_img_67a62d6517b67_1.jpg', 1, 1],
    ['Compact Jazz Set', 'compact-jazz-set', 'Kompaktní jazzový set pro menší prostory. Skvělý zvuk a snadná manipulace.', 9490, null, 'https://www.drumcenter.cz/bin/goods_images/produkt_15/74815_img_4e8ec1147ce18_1.jpg', 1, 1],
    ['Elektronický set', 'elektronicky-set', 'Tichý trénink s výstupy do sluchátek a počítače. Ideální pro byty.', 18900, null, 'https://www.drumcenter.cz/bin/goods_images/produkt_7/99027_img_603d06880c297_debutkit.jpg', 1, 1],
    ['Sabian AAX Crash 16"', 'sabian-aax-crash-16', 'Profesionální crash činel s jasným, prokrojeným zvukem.', 4290, 4990, 'https://www.muziker.cz/cdn/shop/files/sabian-aax-suite-set-cinelu-1.jpg', 2, 1],
    ['Hi-Hat 14"', 'hi-hat-14', 'Hi-hat činel pro univerzální použití.', 5290, null, 'https://www.muziker.cz/cdn/shop/files/sabian-aax-suite-set-cinelu-1.jpg', 2, 0],
    ['Vic Firth 5A paličky', 'vic-firth-5a', 'Klasické dřevěné paličky 5A – nejprodávanější model na světě.', 290, null, 'https://www.muziker.cz/cdn/shop/files/vic-firth-american-classic-5a-pair.jpg', 3, 1],
    ['Pedál Iron Cobra 200', 'iron-cobra-200', 'Jednošlapka s plynulým chodem.', 2490, null, 'https://www.muziker.cz/cdn/shop/files/tama-iron-cobra-200-single-pedal.jpg', 4, 0],
];
foreach ($products as $p) {
    $prod->execute([
        ':n'=>$p[0], ':s'=>$p[1], ':d'=>$p[2], ':p'=>$p[3],
        ':op'=>$p[4], ':i'=>$p[5], ':cid'=>$p[6], ':f'=>$p[7],
    ]);
}

// Seed: parametry (volitelné = select, informační = info)
$par = $pdo->prepare('INSERT INTO product_parameters (product_id, name, value, type) VALUES (:pid, :n, :v, :t)');
$params = [
    [1, 'Barva', 'Černá', 'select'],
    [1, 'Barva', 'Bílá', 'select'],
    [1, 'Barva', 'Červená', 'select'],
    [1, 'Materiál', 'Topol', 'info'],
    [1, 'Počet bubnů', '5', 'info'],
    [2, 'Barva', 'Přírodní', 'select'],
    [2, 'Barva', 'Black Sparkle', 'select'],
    [2, 'Materiál', 'Bříza', 'info'],
    [3, 'Velikost padů', '8"', 'select'],
    [3, 'Velikost padů', '10"', 'select'],
    [4, 'Velikost', '16"', 'info'],
    [4, 'Materiál', 'Bronz B20', 'info'],
    [6, 'Velikost', '5A', 'info'],
];
foreach ($params as $p) {
    $par->execute([':pid'=>$p[0], ':n'=>$p[1], ':v'=>$p[2], ':t'=>$p[3]]);
}

// Seed: obrázky
$img = $pdo->prepare('INSERT INTO product_images (product_id, url, alt) VALUES (:pid, :u, :a)');
$images = [
    [1, 'https://www.drumcenter.cz/bin/goods_images/produkt_6/102066_img_67a62d6517b67_1.jpg', 'Standard Drum Set – pohled 1'],
    [1, 'https://www.drumcenter.cz/bin/goods_images/produkt_15/74815_img_4e8ec1147ce18_1.jpg', 'Standard Drum Set – pohled 2'],
    [2, 'https://www.drumcenter.cz/bin/goods_images/produkt_15/74815_img_4e8ec1147ce18_1.jpg', 'Compact Jazz Set'],
    [3, 'https://www.drumcenter.cz/bin/goods_images/produkt_7/99027_img_603d06880c297_debutkit.jpg', 'Elektronický set'],
    [4, 'https://www.muziker.cz/cdn/shop/files/sabian-aax-suite-set-cinelu-1.jpg', 'Sabian AAX'],
];
foreach ($images as $i) {
    $img->execute([':pid'=>$i[0], ':u'=>$i[1], ':a'=>$i[2]]);
}

// Seed: doprava
$ship = $pdo->prepare('INSERT INTO shipping_methods (name, price, delivery_days) VALUES (:n, :p, :d)');
foreach ([
    ['Osobní odběr (Praha)', 0, 'Ihned po domluvě'],
    ['Zásilkovna', 79, '2–3 pracovní dny'],
    ['Česká pošta – Balík do ruky', 149, '1–2 pracovní dny'],
    ['PPL', 199, '1 pracovní den'],
] as $s) {
    $ship->execute([':n'=>$s[0], ':p'=>$s[1], ':d'=>$s[2]]);
}

// Seed: platba
$pay = $pdo->prepare('INSERT INTO payment_methods (name, fee) VALUES (:n, :f)');
foreach ([
    ['Platba kartou online', 0],
    ['Bankovní převod', 0],
    ['Dobírka', 49],
] as $p) {
    $pay->execute([':n'=>$p[0], ':f'=>$p[1]]);
}

echo "Databáze byla úspěšně inicializována: {$dbPath}\n";
