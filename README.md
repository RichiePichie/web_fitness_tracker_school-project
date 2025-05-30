# Fitness Tracker

Webová aplikace pro sledování cvičení a fitness cílů.

## Popis

Fitness Tracker je webová aplikace, která umožňuje uživatelům sledovat své cvičební aktivity, nastavovat si fitness cíle a sledovat svůj pokrok. Aplikace je vytvořena jako školní projekt pro předmět WEA.

## Funkce

- Registrace a přihlášení uživatelů
- Přidávání, úprava a odstranění cvičení
- Nastavení a sledování fitness cílů
- Statistiky cvičení
- Nahrávání profilových obrázků
- **Admin Panel**:
  - Základní administrační rozhraní pro správu aplikace (dostupné na `/admin/`).
  - V současné fázi obsahuje zástupné symboly pro správu uživatelů, aktivit a nastavení.
  - **Důležité**: Admin panel momentálně nemá implementované zabezpečení. Je nutné ho doplnit před nasazením do produkčního prostředí.

## Technologie

- PHP 7.4+
- MySQL (MariaDB)
- HTML5
- CSS3
- JavaScript

## Instalace

1. Naklonujte repozitář:
   ```
   git clone https://github.com/vas-uzivatelske-jmeno/fitness-tracker.git
   ```

2. Importujte databázi:
   ```
   mysql -u username -p < database.sql
   ```

3. Nakonfigurujte připojení k databázi v souboru `config.php`.

4. Spusťte aplikaci na PHP serveru.

## Autoři

- Adam Přeček(a.precek.st@spseiostrava.cz)
- Daniel Palovský (d.palovsky.st@spseiostrava.cz)
- Richard Pokuta (r.pokuta.st@spseiostrava.cz)

## Licence

Tento projekt je licencován pod MIT licencí.