# Linženýr soft 

<!-- :gb:🏴󠁧󠁢󠁥󠁮󠁧󠁿 [English version of Readme](README.en.md) 🇺🇸🇦🇺 -->

### Menu

- [O projektu](#o-projektu)
- [Instalace](#instalace)
- [Použité zdroje](#použité-zdroje)

## O projektu
Jedná se o školní projekt do předmětu *Řízení softwarových projektů*. Jedná se o webovou aplikaci, s redakčním systémem pro psaní článků. 

**Užitečné odkazy:**

- :writing_hand: [RSP Moodle](https://moodle.vspj.cz/course/view.php?id=202744)
- [Návrh designu](https://www.figma.com/file/zaNmvRBlpfe8Af4A5bMcQs/Untitled?type=design&node-id=0%3A1&mode=design&t=9MpZWB0Z3ptzWzxI-1)


## Instalace

### 1. Naklonujeme repozitář
```
git clone git@github.com:JKtechhw/linzenyr-soft.git && cd linzenyr-soft
```

### 2. Konfigurace databáze

Vytvoříme strukturu databáze

```
mysql -h hostname -u user database < ./Structures/Structure.sql
```

### 3. Zkopírujeme kód do adresáře pro server

Zkopírujeme kód, a poté se do cílového adresáře přesuneme

```
cd -r Code/ *Adresář pro server*
```

### 3. Připojení databáze

Zkopírujeme vzorový soubor *dbExample.php* a uložíme do stejného adresáře pod názvem *db.php*, v tomto souboru vyplníme údaje databáze.

```
cd config/
cp dbExample.php db.php

```
