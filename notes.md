# NOTES

## TODO

-   V rámci TEST-PROBLEM-FINAL-ASSOCIATION zpět doplnit ProblemTemplate -> za účelem dopočtu statistik pro šablony !!!

-   Zobrazovat akce pro stažení ZIP archivu a kompilaci PDF na Overleaf pouze pro již uzavřené testy

-   Akce přegenerování testu
    -   "UPDATE vyvolávající CREATE"
    -   Vlastní formulář
    -   Základní atributy
    -   Seznam úloh ve formě jejich vlastností (tabulka dle filtrů)
    -   Filtry bude nutné zaznamenávat jako samostatnou entitu, která bude v asociaci s testem
    -   Volba úloh, které se mají přegenerovat
    -   Přegenerovat půjde pouze uzavřený test

## Pomůcky

### Regex pro nahrazení obj. přístupu za array přístup

        ->(\w+)\)
        ['$1'])
