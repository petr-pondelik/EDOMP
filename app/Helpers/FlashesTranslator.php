<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.5.19
 * Time: 10:30
 */

namespace App\Helpers;

use App\Exceptions\GeneratorException;
use App\Exceptions\InvalidParameterException;
use App\Exceptions\ProblemDuplicityException;
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
                'default' => 'Šablona úspěšně vytvořena.',
                'update' => 'Šablona úspěšně editována.',
                'delete' => 'Šablona úspěšně odstraněna.',
                'difficulty' => 'Obtížnost úspěšně změněna.',
                'subCategory' => 'Téma úspěšně změněno.'
            ],

            'error' => [
                'default' => 'Chyba při vytváření šablony.',
                'update' => 'Chyba při editaci šablony.',
                'delete' => 'Chyba při odstraňování šablony.',
                'difficulty' => 'Chyba při změně obtížnosti.',
                'subCategory' => 'Chyba při změně tématu.'
            ],

            'constraintViolation' => 'K šabloně existují vygenerované příklady.'
        ],

        'Admin:QuadraticEqTemplate' => [

            'success' => [
                'default' => 'Šablona úspěšně vytvořena.',
                'update' => 'Šablona úspěšně editována.',
                'delete' => 'Šablona úspěšně odstraněna.',
                'difficulty' => 'Obtížnost úspěšně změněna.',
                'subCategory' => 'Téma úspěšně změněno.'
            ],

            'error' => [
                'default' => 'Chyba při vytváření šablony.',
                'update' => 'Chyba při editaci šablony.',
                'delete' => 'Chyba při odstraňování šablony.',
                'difficulty' => 'Chyba při změně obtížnosti.',
                'subCategory' => 'Chyba při změně tématu.'
            ],

            'constraintViolation' => 'K šabloně existují vygenerované příklady.'
        ],

        'Admin:ArithmeticSeqTemplate' => [

            'success' => [
                'default' => 'Šablona úspěšně vytvořena.',
                'update' => 'Šablona úspěšně editována.',
                'delete' => 'Šablona úspěšně odstraněna.',
                'difficulty' => 'Obtížnost úspěšně změněna.',
                'subCategory' => 'Téma úspěšně změněno.'
            ],

            'error' => [
                'default' => 'Chyba při vytváření šablony.',
                'update' => 'Chyba při editaci šablony.',
                'delete' => 'Chyba při odstraňování šablony.',
                'difficulty' => 'Chyba při změně obtížnosti.',
                'subCategory' => 'Chyba při změně tématu.'
            ],

            'constraintViolation' => 'K šabloně existují vygenerované příklady.'
        ],

        'Admin:GeometricSeqTemplate' => [

            'success' => [
                'default' => 'Šablona úspěšně vytvořena.',
                'update' => 'Šablona úspěšně editována.',
                'delete' => 'Šablona úspěšně odstraněna.',
                'difficulty' => 'Obtížnost úspěšně změněna.',
                'subCategory' => 'Téma úspěšně změněno.'
            ],

            'error' => [
                'default' => 'Chyba při vytváření šablony.',
                'update' => 'Chyba při editaci šablony.',
                'delete' => 'Chyba při odstraňování šablony.',
                'difficulty' => 'Chyba při změně obtížnosti.',
                'subCategory' => 'Chyba při změně tématu.'
            ],

            'constraintViolation' => 'K šabloně existují vygenerované příklady.'
        ],

        'Admin:ProblemFinal' => [

            'success' => [
                'default' => 'Příklad úspěšně vytvořen.',
                'update' => 'Příklad úspěšně editován.',
                'delete' => 'Příklad úspěšně odstraněn.',
                'difficulty' => 'Obtížnost úspěšně změněna.',
                'subCategory' => 'Téma úspěšně změněno.',
                'getRes' => 'Výsledek úspěšně získán.'
            ],

            'error' => [
                'default' => 'Chyba při vytváření příkladu.',
                'update' => 'Chyba při editaci příkladu.',
                'delete' => 'Chyba při odstraňování příkladu.',
                'difficulty' => 'Chyba při změně obtížnosti.',
                'subCategory' => 'Chyba při změně tématu.',
                'getRes' => 'Při výpočtu výsledku nastala chyba.'
            ],

            'constraintViolation' => 'Příklad se vyskytuje ve vygenerovaném testu.'

        ],

        'Admin:ProblemType' => [

            'success' => [
                'default' => 'Typ úlohy úspěšně vytvořen.',
                'update' => 'Typ úlohy úspěšně editován.',
                'delete' => 'Typ úlohy úspěšně odstraněn.',
            ],

            'error' => [
                'default' => 'Chyba při vytváření typu úlohy.',
                'update' => 'Chyba při editaci typu úlohy.',
                'delete' => 'Chyba při odstraňování typu úlohy.',
            ]

        ],

        'Admin:Category' => [

            'success' => [
                'default' => 'Kategorie úspěšně vytvořena.',
                'update' => 'Kategorie úspěšně editována.',
                'delete' => 'Kategorie úspěšně odstraněna.',
            ],

            'error' => [
                'default' => 'Chyba při vytváření kategorie.',
                'update' => 'Chyba při editaci kategorie.',
                'delete' => 'Chyba při odstraňování kategorie.',
            ],

            'constraintViolation' => 'Ke kategorii existují úlohy.'

        ],

        'Admin:SubCategory' => [

            'success' => [
                'default' => 'Podkategorie úspěšně vytvořena.',
                'update' => 'Podkategorie úspěšně editována.',
                'delete' => 'Podkategorie úspěšně odstraněna.',
                'category' => 'Kategorie úspěšně změněna.'
            ],

            'error' => [
                'default' => 'Chyba při vytváření podkategorie.',
                'update' => 'Chyba při editaci podkategorie.',
                'delete' => 'Chyba při odstraňování podkategorie.',
                'category' => 'Chybě při změně kategorie.'
            ],

            'constraintViolation' => 'K podkategorii existují úlohy.'

        ],

        'Admin:SuperGroup' => [

            'success' => [
                'default' => 'Superskupina úspěšně vytvořena.',
                'update' => 'Superskupina úspěšně editována.',
                'delete' => 'Superskupina úspěšně odstraněna.',
            ],

            'error' => [
                'default' => 'Chyba při vytváření superskupiny.',
                'update' => 'Chyba při editaci superskupiny.',
                'delete' => 'Chyba při odstraňování superskupiny.',
            ]

        ],

        'Admin:Group' => [

            'success' => [
                'default' => 'Skupina úspěšně vytvořena.',
                'update' => 'Skupina úspěšně editována.',
                'delete' => 'Skupina úspěšně odstraněna.',
                'superGroup' => 'Superskupina úspěšně změněna.'
            ],

            'error' => [
                'default' => 'Chyba při vytváření skupiny.',
                'update' => 'Chyba při editaci skupiny.',
                'delete' => 'Chyba při odstraňování skupiny.',
                'superGroup' => 'Chyba při změně superskupiny.'
            ]

        ],

        'Admin:User' => [

            'success' => [
                'default' => 'Uživatel úspěšně vytvořen.',
                'update' => 'Uživatel úspěšně editován.',
                'delete' => 'Uživatel úspěšně odstraněn.',
            ],

            'error' => [
                'default' => 'Chyba při vytváření uživatele.',
                'update' => 'Chyba při editaci uživatele.',
                'delete' => 'Chyba při odstraňování uživatele.',
            ]

        ],

        'Admin:Logo' => [

            'success' => [
                'default' => 'Logo úspěšně vytvořeno.',
                'update' => 'Logo úspěšně editováno.',
                'delete' => 'Logo úspěšně odstraněno.',
            ],

            'error' => [
                'default' => 'Chyba při vytváření loga.',
                'update' => 'Chyba při editaci loga.',
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
                'close' => 'Test úspěšně uzavřen.',
                'create' => 'Test úspěšně vytvořen.',
                'update' => 'Test úspěšně editován.',
                'delete' => 'Test úspěšně odstraněn.',
            ],

            'error' => [
                'close' => 'Test úspěšně uzavřen.',
                'create' => 'Chyba při tvorbě testu.',
                'update' => 'Chyba při editaci testu.',
                'delete' => 'Chyba při odstraňování testu.',
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
        bdump('TRANSLATE');
        if($e instanceof ForeignKeyConstraintViolationException){
            return self::$presenterMessages[$presenterName]['constraintViolation'];
        }

        if($e instanceof ProblemDuplicityException || $e instanceof InvalidParameterException){
            return $e->getMessage();
        }

        if($e instanceof GeneratorException && $e->isVisible()){
            return $e->getMessage();
        }

        return self::$presenterMessages[$presenterName][$type][$operation];
    }
}