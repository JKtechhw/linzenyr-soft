# Linženýr soft 

<!-- :gb:🏴󠁧󠁢󠁥󠁮󠁧󠁿 [English version of Readme](README.en.md) 🇺🇸🇦🇺 -->

### Menu

- [O projektu](#o-projektu)
- [Instalace](#instalace)
- [Fukce](#funkce)

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


### Testovací data

Pokud chcete před finálním nasazením otestovat funkčnost a rozhraní aplikace, je možné použít testovací data DB

```
mysql -h hostname -u user database < ./Structures/Test-data.sql
```

Pro každého uživatele je **heslo stejné jako login**, seznam uživatelů je v souboru: *Test-data.sql*

## Funkce

### Role

| ID | Název | Popis |
|---|---|---|
| 1 | Autor | Uživatel, který může poze psát články a následně je zasílat ke kontrole redaktorům a recenzentům |
| 2 | Redaktor | Redaktor převezme potvrzený článek od autorů, a pokud je článek v pořádku, publikuje ho na web  |
| 3 | Recenzent | Recenzent kontroluje a hodnotí napsané články od autorů, k těmto článkům se může slovně vyjádřit |
| 4 | Šéfredaktor | Šéfredaktor rozhoduje, jaké články se publikují do jednotlivých vydání časopisu |
| 5 | Administrátor | Administrátor má kompletní kontrolu nad daty |

### Stavy článků

| ID | Název | Popis |
|---|---|---|
| 0 | Rozepsáno | Článek je rozepsán autorem |
| 1 | Schvalování | Článek je schvalován redaktory a recenzenti se k němu můžou vyjádřit, autor nemůže článek upravovat |
| 2 | Zamítnuto | Článek nebyl schválen a autor ho může upravit podle recenzí, nebo ho smazat |
| 3 | Schváleno / publikováno | Článek byl schválen a je publikován na webu, autor již nemůže článek upravovat |
| 4 | Vydáno | Článek byl publikován v některém vydání časopisu |