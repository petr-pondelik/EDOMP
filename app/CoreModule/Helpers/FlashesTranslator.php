<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 25.5.19
 * Time: 10:30
 */

namespace App\CoreModule\Helpers;

use App\TeacherModule\Exceptions\GeneratorException;
use App\TeacherModule\Exceptions\InvalidParameterException;
use App\TeacherModule\Exceptions\ProblemDuplicityException;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

/**
 * Class FlashesTranslator
 * @package App\CoreModule\Helpers
 */
class FlashesTranslator
{
    /**
     * @var array
     */
    protected static $presenterMessages = [

        'Teacher:LinearEqTemplate' => [

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

        'Teacher:QuadraticEqTemplate' => [

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

        'Teacher:ArithmeticSeqTemplate' => [

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

        'Teacher:GeometricSeqTemplate' => [

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

        'Teacher:ProblemFinal' => [

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

        'Teacher:ProblemType' => [

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

        'Teacher:Category' => [

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

        'Teacher:SubCategory' => [

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

        'Teacher:SuperGroup' => [

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

        'Teacher:Group' => [

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

        'Teacher:User' => [

            'success' => [
                'default' => 'Uživatel úspěšně vytvořen.',
                'update' => 'Uživatel úspěšně editován.',
                'delete' => 'Uživatel úspěšně odstraněn.',
                'resendPassword' => 'Heslo úspěšně aktualizováno a odesláno.'
            ],

            'error' => [
                'default' => 'Chyba při vytváření uživatele.',
                'update' => 'Chyba při editaci uživatele.',
                'delete' => 'Chyba při odstraňování uživatele.',
                'resendPassword' => 'Chyba při aktualizaci hesla.'
            ],

            'uniqueConstraintViolation' => 'Uživatel se zadanným e-mailem či uživatelským jménem již existuje.'

        ],

        'Teacher:Logo' => [

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

        'Teacher:Settings' => [

            'success' => [
                'groupPermissions' => 'Oprávnění skupiny úspěšně změněna.',
                'superGroupPermissions' => 'Oprávnění superskupiny úspěšně změněna.',
                'password' => 'Heslo úspěšně změněno.',
                'testTemplate' => 'Šablona testu úspěšně upravena.'
            ],

            'error' => [
                'groupPermissions' => 'Chyba při změně oprávnění skupiny.',
                'superGroupPermissions' => 'Chyba při změně oprávnění superskupiny.',
                'password' => 'Chyba při změně hesla.',
                'testTemplate' => 'Chyba při úpravě šablony testu.'
            ]

        ],

        'Teacher:Test' => [

            'success' => [
                'close' => 'Test úspěšně uzavřen.',
                'create' => 'Test úspěšně vytvořen.',
                'update' => 'Test úspěšně editován.',
                'delete' => 'Test úspěšně odstraněn.',
                'regenerate' => 'Test úspěšně přegenerován.',
                'downloadSource' => 'Archiv s testem stažen.'
            ],

            'error' => [
                'close' => 'Test úspěšně uzavřen.',
                'create' => 'Chyba při tvorbě testu.',
                'update' => 'Chyba při editaci testu.',
                'delete' => 'Chyba při odstraňování testu.',
                'regenerate' => 'Chyba během přegenerování testu.',
                'downloadSource' => 'Chyba během stahování archivu s testem.'
            ]

        ],

        'Student:Settings' => [

            'success' => [
                'password' => 'Heslo úspěšně změněno.'
            ],

            'error' => [
                'password' => 'Chyba při změně hesla.'
            ]

        ]

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

        if($e instanceof UniqueConstraintViolationException){
            return self::$presenterMessages[$presenterName]['uniqueConstraintViolation'];
        }

        if (
            $e instanceof ProblemDuplicityException ||
            $e instanceof InvalidParameterException ||
            ($e instanceof GeneratorException && $e->isVisible())
        ){
            return $e->getMessage();
        }

        return self::$presenterMessages[$presenterName][$type][$operation];
    }
}