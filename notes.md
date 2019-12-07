# NOTES

## TODO

-   Vylepšení v systému generování:
    -   Již na vstupu provést trimm a odstranění duplicitních mezer!!!
    -   Odstraňovat duplicitní mezery a závorky ve výrazech!!!

-   Testy
    -   Je potřeba zbavit se využívání konstant definovaných před define() !!!

## Pomůcky

### Regex pro nahrazení obj. přístupu za array přístup

        ->(\w+)\)
        ['$1'])
