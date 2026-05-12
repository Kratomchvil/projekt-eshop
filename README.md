# DrumShop – e-shop (2. fáze: PHP + SQLite)

Kompletní funkční e-shop postavený na PHP 8.3 a SQLite. Druhá fáze projektu pro předmět PRG.

## Spuštění

### V Codespaces / lokálně

1. Inicializujte databázi:
   ```bash
   php database/init.php
   ```
2. Spusťte vestavěný PHP server v rootu projektu:
   ```bash
   php -S 0.0.0.0:8000
   ```
3. Otevřete v prohlížeči `http://localhost:8000`.

> Soubor `database/eshop.db` je v `.gitignore` a generuje se přes `init.php`.

## Struktura

```
/projekt-eshop
├── index.php, kategorie.php, produkty.php, produkt.php
├── kosik.php, objednavka-1.php, objednavka-2.php, objednavka-3.php
├── objednavka-potvrzeni.php, vyhledavani.php, o-nas.php, kontakt.php, 404.php
├── /src
│   ├── bootstrap.php, Database.php, Cart.php, Validator.php
│   ├── /DTO   (CategoryDTO, ProductDTO, …)
│   └── /Repository (CategoryRepository, ProductRepository, …)
├── /partials  (header.php, footer.php, product-card.php)
├── /database  (init.php, eshop.db)
└── /assets    (css, js, logo, images)
```

## Implementované funkce

- Dynamický výpis kategorií a produktů z databáze
- Detail produktu s galerií, parametry, variantami a slevou
- Košík v session: přidání, změna množství, odebrání, podpora variant
- Třístupňová objednávka (dodací údaje → doprava/platba → shrnutí)
- Uložení zákazníka a objednávky do DB, vyprázdnění košíku, PRG
- Vyhledávání produktů
- Vlastní 404 stránka
- Server-side validace formulářů přes `Validator` (fluent interface)
- Bezpečnost: `htmlspecialchars()` u všech výpisů, prepared statements, PRG

## Hodnocené best practices

- `declare(strict_types=1);` v každém PHP souboru
- DRY – partials `header.php`, `footer.php`, `product-card.php`
- DTO + repozitáře, žádné inline SQL ve stránkách
- Žádné inline `<style>` bloky – nové styly v `assets/css/php-extras.css`
