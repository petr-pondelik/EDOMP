# TESTOVACÍ SCÉNÁŘE APLIKACE

## TS1: Operace nad uživateli

-   Přihlásit se jako uživatel:
    -   login: admin
    -   password: 12345678
-   Nalezení položky Uživatelé v hlavní nabídce -> vstup do sekce.
-   Kliknutí na ikonu přidat a vytvoření uživatele.
-   Dohledání uživatele v seznamu.
-   Nalezení ikony editace -> vstup do sekce.
-   Editace přihlašovacího jména, hesla a přiřazení do jiné skupiny.
-   Uložení změn
-   Vyhledání uživatele "Tom Speed".

## TS2: Operace nad příklady

-   Nalezení položky Příklady v hlavní nabídce -> vstup do sekce.
-   Kliknutí na ikonu přidat
-   Volba parametrů příkladu
-   Zadání příkladu ve tvaru:
    -   14x + \big(2 - 5 + 4x\big) + \frac{12x}{5} = \frac{\big(5x + x\big)}{10}
-   Nalezení ikony editace vytvořeného příkladu -> vstup do sekce
-   Editace příkladu:
    -   Změna obtížnosti
    -   Úprava tvaru: 14x + (2 - 5 + 4x) + 12x/5 = 0
-   Uložení změn

## TS3: Operace nad šablonami

-   Nalezení položky Šablony -> vstup do sekce Kvadratické rovnice
-   Kliknutí na ikonu přidat
-   Vytvoření šablony kv. rovnice:
    -   $$ 5 x^2 + 2 + \big(<par min="0" max="8"/> - 3 + 3 x\big) = <par min="0" max="10"/> $$
    -   Neznámá: x
    -   Podmínka diskriminantu: nulový
        -> Po chybové hlášce změna: kladný
- Nalezení ikony editace šablony -> vstup do sekce
-   Editace šablony
    -   Úprava tvaru: $$ 5 x^2 + 2 + \big(<par min="0" max="8"/> - 3 + 3 x\big) = 0 $$
    -   Ověření splnitelnosti, potvrzení změn

## TS4: Upload loga

-   Nalezení položky Loga v hlavní nabídce -> vstup do sekce
-   Kliknutí na ikonu přidat
-   Zvolit název loga a uploadovat vybraný soubor
-   Zobrazit si detail loga
-   Nalezení ikony editace loga -> vstup do sekce
-   Zvolit změnu souboru logu a uploadovat nový soubor
-   Potvrdit změny

## TS5: Vygenerování testu

-   Nalezení položky Testy v hlavní nabídce -> vstup do sekce
-   Kliknutí na ikonu přidat
-   Zvolit počet variant testu
-   Zvolit logo
-   Zvolit cílové skupiny
-   Vyplnit školní rok, období a číslo testu
-   Zadat do testu 2 úlohy
    -   první náhodně zvolená úloha, která není šablona
    -   druhá úloha: vytvořená šablona
-   Kliknout na vytvořit
-   Dohledat test v seznamu a zadat úspěšnost úloh v testu

## TS6: Správa oprávnění k úlohám

-   Nalezení položky Nastavení v hlavní nabídce -> vstup do sekce
-   Zvolit Oprávnění skupin
-   Nastavit skupině 1.A oprávnění ke kategoriím úloh: Rovnice, Posloupnosti
-   Potvrdit změny

## TS7: Použití sbírky úloh

-   Přihlásit se jako uživatel:
    -   login: Bart_Oakley9229@mafthy.com
    -   password: 12345678
-   Nalezení sbírky pro Rovnice v hlavní nabídce
-   Nalezení příkladu vygenerovaného ze zadané šablony
    -   Průchodem stránkami sbírky pro rovnice
-   Filtrování:
    -   Obtížnost: Střední
    -   Téma: Kvadratická rovnice
-   Reset filtrů
-   Odhlášení
