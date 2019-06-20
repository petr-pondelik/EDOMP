<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.5.19
 * Time: 10:30
 */

namespace App\Helpers;

use App\Exceptions\InvalidParameterException;
use App\Exceptions\ProblemFinalCollisionException;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;

/**
 * Class ExceptionTranslator
 * @package App\Helpers
 */
class FlashesTranslator
{
    /**
     * @var array
     */
    protected static $presenterMessages = [

        'Admin:LinearEqTemplate' => [

            'success' => [
                'create' => 'Šablona úspěšně vytvořena.',
                'edit' => 'Šablona úspěšně editována.',
                'delete' => 'Šablona úspěšně odstraněna.',
                'difficulty' => 'Obtížnost úspěšně změněna.',
                'subCategory' => 'Téma úspěšně změněno.'
            ],

            'error' => [
                'create' => 'Chyba při vytváření šablony.',
                'edit' => 'Chyba při editaci šablony.',
                'delete' => 'Chyba při odstraňování šablony.',
                'difficulty' => 'Chyba při změně obtížnosti.',
                'subCategory' => 'Chyba při změně tématu.'
            ],

            'constraintViolation' => 'K šabloně existují vygenerované příklady.'
        ],

        'Admin:QuadraticEqTemplate' => [

            'success' => [
                'create' => 'Šablona úspěšně vytvořena.',
                'edit' => 'Šablona úspěšně editována.',
                'delete' => 'Šablona úspěšně odstraněna.',
                'difficulty' => 'Obtížnost úspěšně změněna.',
                'subCategory' => 'Téma úspěšně změněno.'
            ],

            'error' => [
                'create' => 'Chyba při vytváření šablony.',
                'edit' => 'Chyba při editaci šablony.',
                'delete' => 'Chyba při odstraňování šablony.',
                'difficulty' => 'Chyba při změně obtížnosti.',
                'subCategory' => 'Chyba při změně tématu.'
            ],

            'constraintViolation' => 'K šabloně existují vygenerované příklady.'
        ],

        'Admin:ArithmeticSeqTemplate' => [

            'success' => [
                'create' => 'Šablona úspěšně vytvořena.',
                'edit' => 'Šablona úspěšně editována.',
                'delete' => 'Šablona úspěšně odstraněna.',
                'difficulty' => 'Obtížnost úspěšně změněna.',
                'subCategory' => 'Téma úspěšně změněno.'
            ],

            'error' => [
                'create' => 'Chyba při vytváření šablony.',
                'edit' => 'Chyba při editaci šablony.',
                'delete' => 'Chyba při odstraňování šablony.',
                'difficulty' => 'Chyba při změně obtížnosti.',
                'subCategory' => 'Chyba při změně tématu.'
            ],

            'constraintViolation' => 'K šabloně existují vygenerované příklady.'
        ],

        'Admin:GeometricSeqTemplate' => [

            'success' => [
                'create' => 'Šablona úspěšně vytvořena.',
                'edit' => 'Šablona úspěšně editována.',
                'delete' => 'Šablona úspěšně odstraněna.',
                'difficulty' => 'Obtížnost úspěšně změněna.',
                'subCategory' => 'Téma úspěšně změněno.'
            ],

            'error' => [
                'create' => 'Chyba při vytváření šablony.',
                'edit' => 'Chyba při editaci šablony.',
                'delete' => 'Chyba při odstraňování šablony.',
                'difficulty' => 'Chyba při změně obtížnosti.',
                'subCategory' => 'Chyba při změně tématu.'
            ],

            'constraintViolation' => 'K šabloně existují vygenerované příklady.'
        ],

        'Admin:ProblemFinal' => [

            'success' => [
                'create' => 'Příklad úspěšně vytvořen.',
                'edit' => 'Příklad úspěšně editován.',
                'delete' => 'Příklad úspěšně odstraněn.',
                'difficulty' => 'Obtížnost úspěšně změněna.',
                'subCategory' => 'Téma úspěšně změněno.',
                'getRes' => 'Výsledek úspěšně získán.'
            ],

            'error' => [
                'create' => 'Chyba při vytváření příkladu.',
                'edit' => 'Chyba při editaci příkladu.',
                'delete' => 'Chyba při odstraňování příkladu.',
                'difficulty' => 'Chyba při změně obtížnosti.',
                'subCategory' => 'Chyba při změně tématu.',
                'getRes' => 'Při výpočtu výsledku nastala chyba.'
            ],

            'constraintViolation' => 'Příklad se vyskytuje ve vygenerovaném testu.'

        ],

        'Admin:ProblemType' => [

            'success' => [
                'create' => 'Typ úlohy úspěšně vytvořen.',
                'edit' => 'Typ úlohy úspěšně editován.',
                'delete' => 'Typ úlohy úspěšně odstraněn.',
            ],

            'error' => [
                'create' => 'Chyba při vytváření typu úlohy.',
                'edit' => 'Chyba při editaci typu úlohy.',
                'delete' => 'Chyba při odstraňování typu úlohy.',
            ]

        ],

        'Admin:Category' => [

            'success' => [
                'create' => 'Kategorie úspěšně vytvořena.',
                'edit' => 'Kategorie úspěšně editována.',
                'delete' => 'Kategorie úspěšně odstraněna.',
            ],

            'error' => [
                'create' => 'Chyba při vytváření kategorie.',
                'edit' => 'Chyba při editaci kategorie.',
                'delete' => 'Chyba při odstraňování kategorie.',
            ],

            'constraintViolation' => 'Ke kategorii existují úlohy.'

        ],

        'Admin:SubCategory' => [

            'success' => [
                'create' => 'Podkategorie úspěšně vytvořena.',
                'edit' => 'Podkategorie úspěšně editována.',
                'delete' => 'Podkategorie úspěšně odstraněna.',
                'category' => 'Kategorie úspěšně změněna.'
            ],

            'error' => [
                'create' => 'Chyba při vytváření podkategorie.',
                'edit' => 'Chyba při editaci podkategorie.',
                'delete' => 'Chyba při odstraňování podkategorie.',
                'category' => 'Chybě při změně kategorie.'
            ],

            'constraintViolation' => 'K podkategorii existují úlohy.'

        ],

        'Admin:SuperGroup' => [

            'success' => [
                'create' => 'Superskupina úspěšně vytvořena.',
                'edit' => 'Superskupina úspěšně editována.',
                'delete' => 'Superskupina úspěšně odstraněna.',
            ],

            'error' => [
                'create' => 'Chyba při vytváření superskupiny.',
                'edit' => 'Chyba při editaci superskupiny.',
                'delete' => 'Chyba při odstraňování superskupiny.',
            ]

        ],

        'Admin:Group' => [

            'success' => [
                'create' => 'Skupina úspěšně vytvořena.',
                'edit' => 'Skupina úspěšně editována.',
                'delete' => 'Skupina úspěšně odstraněna.',
                'superGroup' => 'Superskupina úspěšně změněna.'
            ],

            'error' => [
                'create' => 'Chyba při vytváření skupiny.',
                'edit' => 'Chyba při editaci skupiny.',
                'delete' => 'Chyba při odstraňování skupiny.',
                'superGroup' => 'Chyba při změně superskupiny.'
            ]

        ],

        'Admin:User' => [

            'success' => [
                'create' => 'Uživatel úspěšně vytvořen.',
                'edit' => 'Uživatel úspěšně editován.',
                'delete' => 'Uživatel úspěšně odstraněn.',
            ],

            'error' => [
                'create' => 'Chyba při vytváření uživatele.',
                'edit' => 'Chyba při editaci uživatele.',
                'delete' => 'Chyba při odstraňování uživatele.',
            ]

        ],

        'Admin:Logo' => [

            'success' => [
                'create' => 'Logo úspěšně vytvořeno.',
                'edit' => 'Logo úspěšně editováno.',
                'delete' => 'Logo úspěšně odstraněno.',
            ],

            'error' => [
                'create' => 'Chyba při vytváření loga.',
                'edit' => 'Chyba při editaci loga.',
                'delete' => 'Chyba při odstraňování loga.',
            ],

            'constraintViolation' => 'Logo je využíváno alespoň v jednom vygenerovaném testu.'

        ],

        'Admin:Settings' => [

            'success' => [
                'groupPermissions' => 'Oprávnění skupiny úspěšně změněna.',
                'superGroupPermissions' => 'Oprávnění superskupiny úspěšně změněna.',
            ],

            'error' => [
                'groupPermissions' => 'Chyba při změně oprávnění skupiny.',
                'superGroupPermissions' => 'Chyba při změně oprávnění superskupiny.',
            ]

        ],

        'Admin:Test' => [

            'success' => [
                'create' => 'Test úspěšně vytvořen.',
                'delete' => 'Test úspěšně vytvořen.',
                'statistics' => 'Statistika testu úspěšně editována.'
            ],

            'error' => [
                'create' => 'Chyba při tvorbě testu.',
                'delete' => 'Chyba při tvorbě testu',
                'statistics' => 'Chyba při editaci statistiky testu.'
            ]

        ],

    ];

    /**
     * @param string $operation
     * @param \Exception $e
     * @param string $presenterName
     * @param string|null $type
     * @return string
     */
    public static function translate(string $operation, string $presenterName, string $type = null, \Exception $e = null): string
    {
        if($e instanceof ForeignKeyConstraintViolationException){
            return self::$presenterMessages[$presenterName]['constraintViolation'];
        }

        if($e instanceof ProblemFinalCollisionException || $e instanceof InvalidParameterException){
            return $e->getMessage();
        }

        return self::$presenterMessages[$presenterName][$type][$operation];
    }
}