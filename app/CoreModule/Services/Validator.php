<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 17:36
 */

namespace App\CoreModule\Services;

use App\CoreModule\Arguments\ValidatorArgument;
use App\CoreModule\Exceptions\ValidatorException;
use App\CoreModule\Helpers\ConstHelper;
use App\CoreModule\Helpers\LatexHelper;
use App\CoreModule\Helpers\RegularExpressions;
use App\CoreModule\Helpers\StringsHelper;
use App\CoreModule\Model\Persistent\Functionality\TemplateJsonDataFunctionality;
use App\CoreModule\Model\Persistent\Repository\UserRepository;
use App\TeacherModule\Model\NonPersistent\Entity\ArithmeticSequenceTemplateNP;
use App\TeacherModule\Model\NonPersistent\Entity\GeometricSequenceTemplateNP;
use App\TeacherModule\Model\NonPersistent\Entity\LinearEquationTemplateNP;
use App\TeacherModule\Model\NonPersistent\Entity\ProblemTemplateNP;
use App\TeacherModule\Model\NonPersistent\Entity\QuadraticEquationTemplateNP;
use App\TeacherModule\Plugins\ArithmeticSequencePlugin;
use App\TeacherModule\Plugins\GeometricSequencePlugin;
use App\TeacherModule\Plugins\LinearEquationPlugin;
use App\TeacherModule\Plugins\QuadraticEquationPlugin;
use App\TeacherModule\Services\NewtonApiClient;
use Nette\Application\UI\Form;
use Nette\NotSupportedException;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;
use Nette\Utils\Validators;

/**
 * Class Validator
 * @package App\CoreModule\Services
 */
class Validator
{
    /**
     * @var NewtonApiClient
     */
    protected $newtonApiClient;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var TemplateJsonDataFunctionality
     */
    protected $templateJsonDataFunctionality;

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * @var StringsHelper
     */
    protected $stringsHelper;

    /**
     * @var LatexHelper
     */
    protected $latexHelper;

    /**
     * @var array
     */
    protected $validationMapping;

    /**
     * @var array
     */
    protected $validationMessages;

    /**
     * @var LinearEquationPlugin
     */
    protected $linearEquationPlugin;

    /**
     * @var QuadraticEquationPlugin
     */
    protected $quadraticEquationPlugin;

    /**
     * @var ArithmeticSequencePlugin
     */
    protected $arithmeticSequencePlugin;

    /**
     * @var GeometricSequencePlugin
     */
    protected $geometricSequencePlugin;

    /**
     * @var RegularExpressions
     */
    protected $regularExpressions;

    /**
     * @var array
     */
    static protected $bodyMessages = [
        0 => 'Tělo úlohy musí být vyplněno.',
        1 => 'Vstupní LaTeX musí být uvnitř značek pro matematický mód.',
        2 => 'Šablona neobsahuje zadanou neznámou.',
        3 => 'Šablona není validní matematický výraz.'
    ];

    /**
     * Validator constructor.
     * @param NewtonApiClient $newtonApiClient
     * @param Validators $validators
     * @param UserRepository $userRepository
     * @param TemplateJsonDataFunctionality $templateJsonDataFunctionality
     * @param ConstHelper $constHelper
     * @param StringsHelper $stringsHelper
     * @param LatexHelper $latexHelper
     * @param LinearEquationPlugin $linearEquationPlugin
     * @param QuadraticEquationPlugin $quadraticEquationPlugin
     * @param ArithmeticSequencePlugin $arithmeticSequencePlugin
     * @param GeometricSequencePlugin $geometricSequencePlugin
     * @param RegularExpressions $regularExpressions
     */
    public function __construct
    (
        NewtonApiClient $newtonApiClient,
        UserRepository $userRepository,
        TemplateJsonDataFunctionality $templateJsonDataFunctionality,
        ConstHelper $constHelper, StringsHelper $stringsHelper, LatexHelper $latexHelper,
        LinearEquationPlugin $linearEquationPlugin,
        QuadraticEquationPlugin $quadraticEquationPlugin,
        ArithmeticSequencePlugin $arithmeticSequencePlugin,
        GeometricSequencePlugin $geometricSequencePlugin,
        RegularExpressions $regularExpressions
    )
    {
        $this->newtonApiClient = $newtonApiClient;
        $this->userRepository = $userRepository;
        $this->templateJsonDataFunctionality = $templateJsonDataFunctionality;
        $this->constHelper = $constHelper;
        $this->stringsHelper = $stringsHelper;
        $this->latexHelper = $latexHelper;
        $this->linearEquationPlugin = $linearEquationPlugin;
        $this->quadraticEquationPlugin = $quadraticEquationPlugin;
        $this->arithmeticSequencePlugin = $arithmeticSequencePlugin;
        $this->geometricSequencePlugin = $geometricSequencePlugin;
        $this->regularExpressions = $regularExpressions;

        $this->validationMapping = [

            'login' => static function ($data) {
                if (Validators::isNone($data)) {
                    return 0;
                }
                if (!Validators::is($data, 'string:..128')) {
                    return 1;
                }
                return -1;
            },

            'notEmpty' => static function ($data) {
                if (empty($data)) {
                    return 0;
                }
                return -1;
            },

            'arrayNotEmpty' => static function ($data) {
                if (count($data) < 1) {
                    return 0;
                }
                return -1;
            },

            'stringNotEmpty' => static function ($data) {
                if (empty($data)) {
                    return 0;
                }
                if (strlen($data) > 128) {
                    return 1;
                }
                return -1;
            },

            'isTrue' => static function ($data) {
                if (!$data) {
                    return 0;
                }
                return -1;
            },

            'intNotNegative' => static function ($data) {
                if (empty($data)) {
                    return 0;
                }
                if (!is_numeric($data)) {
                    return 1;
                }
                if ($data < 0) {
                    return 2;
                }
                return -1;
            },

            'email' => static function ($data) {
                if (empty($data)) {
                    return 0;
                }
                if (strlen($data) > 128) {
                    return 1;
                }
                if (!Validators::isEmail($data)) {
                    return 2;
                }
                return -1;
            },

            'username' => static function ($data) {
                if (strlen($data) > 128) {
                    return 0;
                }
                return -1;
            },

            // Validate password in Teacher module
            'passwordConfirm' => static function (ArrayHash $data) {
                if (empty($data->password) || empty($data->passwordConfirm)) {
                    return 0;
                }
                if (strcmp($data->password, $data->passwordConfirm)) {
                    return 0;
                }
                if (strlen($data->password) < 8) {
                    return 1;
                }
                return -1;
            },

            'schoolYear' => static function ($filledVal) use ($regularExpressions) {
                if (empty($filledVal)) {
                    return 0;
                }
                if (!Strings::match($filledVal, '~' . $regularExpressions::RE_SCHOOL_YEAR . '~')) {
                    return 1;
                }
                return -1;
            },

            'range0to1' => static function ($filledVAl) {
                if (!empty($filledVAl)) {
                    $filledVAl = Strings::replace($filledVAl, '~,~', '.');
                    if (!is_numeric($filledVAl)) {
                        return 0;
                    }
                    if ($filledVAl < 0 || $filledVAl > 1) {
                        return 1;
                    }
                }
                return -1;
            },

            'body_' . $this->constHelper::LINEAR_EQ => function (LinearEquationTemplateNP $problemTemplate) {
                if (empty($problemTemplate->getBody())) {
                    return 0;
                }
                return $this->linearEquationPlugin->validateBody($problemTemplate);
            },

            'body_' . $this->constHelper::QUADRATIC_EQ => function (QuadraticEquationTemplateNP $problemTemplate) {
                if (empty($problemTemplate->getBody())) {
                    return 0;
                }
                return $this->linearEquationPlugin->validateBody($problemTemplate);
            },

            'body_' . $this->constHelper::ARITHMETIC_SEQ => function (ArithmeticSequenceTemplateNP $problemTemplate) {
                if (empty($problemTemplate->getBody())) {
                    return 0;
                }
                return $this->arithmeticSequencePlugin->validateBody($problemTemplate);
            },

            'body_' . $this->constHelper::GEOMETRIC_SEQ => function (GeometricSequenceTemplateNP $problemTemplate) {
                if (empty($problemTemplate->getBody())) {
                    return 0;
                }
                return $this->geometricSequencePlugin->validateBody($problemTemplate);
            },

            'variable' => static function ($data) {
                return self::validVariable($data);
            },

            'notEmptyPositive' => static function ($filledVal) {
                if (empty($filledVal)) {
                    return 0;
                }
                if ($filledVal <= 0) {
                    return 1;
                }
                return -1;
            },

            'condition_' . $this->constHelper::RESULT => function (ProblemTemplateNP $data) {
                // Maximal number of parameters exceeded
                if ($data->getParametersData()->getCount() > $this->constHelper::PARAMETERS_MAX) {
                    return 2;
                }
                // Maximal parameters complexity exceeded
                if ($data->getParametersData()->getComplexity() > $this->constHelper::COMPLEXITY_MAX) {
                    return 3;
                }
                if (!$this->linearEquationPlugin->validateResultCond($data)) {
                    return 4;
                }
                return -1;
            },

            'condition_' . $this->constHelper::DISCRIMINANT => function (ProblemTemplateNP $data) {
                // Maximal number of parameters exceeded
                if ($data->getParametersData()->getCount() > $this->constHelper::PARAMETERS_MAX) {
                    return 2;
                }
                // Maximal parameters complexity exceeded
                if ($data->getParametersData()->getComplexity() > $this->constHelper::COMPLEXITY_MAX) {
                    return 3;
                }
                if (!$this->quadraticEquationPlugin->validateDiscriminantCond($data)) {
                    return 4;
                }
                return -1;
            },

            'testTemplateContent' => function (string $data) {

                $data = $this->stringsHelper::removeWhiteSpaces($data);
                bdump($data);

                if (Validators::isNone($data)) {
                    return 0;
                }

                if (!Strings::match($data, '~' . $this->regularExpressions::RE_TEST_TEMPLATE . '~')) {
                    return 1;
                }

                return -1;

            }
        ];

        $this->validationMessages = [

            'email' => [
                0 => 'Zadejte e-mail.',
                1 => 'E-mail nesmí být delší než 128 znaků.',
                2 => 'Zadejte validní e-mail.'
            ],

            'username' => [
                0 => 'Uživatelské jméno nesmí být delší než 128 znaků.',
            ],

            'login' => [
                0 => 'Zadejte e-mail či uživatelské jméno.',
                1 => 'Údaj nesmí být delší než 128 znaků.'
            ],

            'password' => [
                0 => 'Zadejte heslo.'
            ],

            'passwordConfirm' => [
                0 => 'Obě hesla musí být vyplněna a shodovat se.',
                1 => 'Heslo musí mít délku alespoň 8 znaků.'
            ],

            'groups' => [
                0 => 'Zvolte alespoň jednu skupinu.'
            ],

            'label' => [
                0 => 'Název musí být vyplněn.',
                1 => 'Název nesmí být delší než 128 znaků.'
            ],

            'logo' => [
                0 => 'Zvolte prosím logo.'
            ],

            'schoolYear' => [
                0 => 'Školní rok musí bý vyplněn.',
                1 => 'Školní roku musí být ve formátu rrrr/rr(rr) nebo rrrr-rr(rr).'
            ],

            'testNumber' => [
                0 => 'Číslo testu musí být vyplněno.',
                1 => 'Číslo testu musí být celé číslo.',
                2 => 'Číslo testu nesmí být záporné.'
            ],

            'testTerm' => [
                0 => 'Období testu musí být vyplněno.'
            ],

            'success_rate' => [
                0 => 'Úspěšnost úlohy musí být číselná hodnota.',
                1 => 'Úspěšnost úlohy musí být v intervalu <0; 1>.'
            ],

            'body' => self::$bodyMessages,
            'body_' . $this->constHelper::LINEAR_EQ => self::$bodyMessages,
            'body_' . $this->constHelper::QUADRATIC_EQ => self::$bodyMessages,
            'body_' . $this->constHelper::ARITHMETIC_SEQ => self::$bodyMessages,
            'body_' . $this->constHelper::GEOMETRIC_SEQ => self::$bodyMessages,

            'variable' => [
                0 => 'Zadejte prosím neznámou.',
                1 => 'Zadejte prosím právě jedno malé písmo abecedy.',
                2 => 'Proměnná nesmí být symbol e.'
            ],

            'firstN' => [
                0 => 'Zvolte počet prvních členů.',
                1 => 'Počet prvních členů musí být kladný.'
            ],

            'category' => [
                0 => 'Zvolte prosím kategorii.'
            ],

            'subCategory' => [
                0 => 'Zvolte prosím podkategorii.'
            ],

            'difficulty' => [
                0 => 'Zvolte prosím obtížnost.'
            ],

            'problemType' => [
                0 => 'Zvolte prosím typ úlohy.'
            ],

            'type_' . $this->constHelper::LINEAR_EQ => [
                0 => 'Zvolte prosím typ úlohy.',
                1 => 'Zadaná úloha není lineární rovnicí.'
            ],

            'type_' . $this->constHelper::QUADRATIC_EQ => [
                0 => 'Zvolte prosím typ úlohy.',
                1 => 'Zadaná úloha není kvadratickou rovnicí.'
            ],

            'type_' . $this->constHelper::ARITHMETIC_SEQ => [
                0 => 'Zvolte prosím typ úlohy.',
                1 => 'Zadaná úloha není aritmetickou posloupností.'
            ],

            'type_' . $this->constHelper::GEOMETRIC_SEQ => [
                0 => 'Zvolte prosím typ úlohy.',
                1 => 'Zadaná úloha není geometrickou posloupností.'
            ],

            'condition_' . $this->constHelper::RESULT => [
                0 => 'Struktura musí být vyplněna',
                1 => 'Chybný formát vstupního LaTeXu.',
                2 => 'Překročen povolený počet parametrů. (maximálně ' . $this->constHelper::PARAMETERS_MAX . ')-',
                3 => 'Překročena povolená složitost parametrů. (maximálně ' . $this->constHelper::COMPLEXITY_MAX . ').',
                4 => 'Podmínka není splnitelná.'
            ],

            'condition_' . $this->constHelper::DISCRIMINANT => [
                0 => 'Struktura úlohy musí být vyplněna.',
                1 => 'Chybný formát vstupního LaTeXu.',
                2 => 'Překročen povolený počet parametrů. (maximálně ' . $this->constHelper::PARAMETERS_MAX . ').',
                3 => 'Překročena povolená složitost parametrů. (maximálně ' . $this->constHelper::COMPLEXITY_MAX . ').',
                4 => 'Podmínka není splnitelná.'
            ],

            'role' => [
                0 => 'Zvolte prosím roli.'
            ],

            'superGroup' => [
                0 => 'Zvolte prosím superskupinu.'
            ],

            'templateContent' => [
                0 => 'Obsah šablony nesmí být prázdný.',
                1 => 'PHP kód šablony nesmí být změněn.'
            ]

        ];
    }

    /**
     * @param $data
     * @return int
     */
    public static function validVariable($data): int
    {
        if (empty($data)) {
            return 0;
        }
        $matches = Strings::match($data, '~^[a-z]$~');
        if (!$matches || strlen($data) !== 1 || count($matches) !== 1) {
            return 1;
        }
        if ($data === 'e') {
            return 2;
        }
        return -1;
    }

    /**
     * @param Form $form
     * @param ValidatorArgument[] $fields
     * @return Form
     * @throws ValidatorException
     */
    public function validate(Form $form, array $fields): Form
    {
        foreach ($fields as $field => $item) {

            $validationRule = $item->validationRule;
            $data = $item->data;

            // Check if the validator supports entered validation
            if (!array_key_exists($validationRule, $this->validationMapping)) {
                throw new NotSupportedException('Validátor: Požadavek obsahuje neočekávanou hodnotu.');
            }

            if (($validationRes = $this->validationMapping[$validationRule]($data)) !== -1) {

                if (isset($this->validationMessages[$field][$validationRes])) {
                    if (isset($item->display)) {
                        $form[$item->display]->addError($this->validationMessages[$field][$validationRes]);
                    } else {
                        $form[$field]->addError($this->validationMessages[$field][$validationRes]);
                    }
                } else {
                    if (isset($item->display)) {
                        if (!isset($this->validationMessages[$validationRule][$validationRes])) {
                            throw new ValidatorException('Zpráva pro validované pole nebyla definována.');
                        }
                        $form[$item->display]->addError($this->validationMessages[$validationRule][$validationRes]);
                    } else {
                        $form[$field]->addError($this->validationMessages[$validationRule][$validationRes]);
                    }
                }
            }
        }

        return $form;
    }

    /**
     * @param ValidatorArgument[] $fields
     * @return array
     * @throws ValidatorException
     */
    public function validatePlain(array $fields): array
    {
        $errors = [];

        foreach ($fields as $field => $item) {
            $validationRule = $item->validationRule;
            $data = $item->data;

            // Check if the validator supports entered validation
            if (!array_key_exists($validationRule, $this->validationMapping)) {
                throw new NotSupportedException('Validátor: Požadavek obsahuje neočekávanou hodnotu.');
            }

            if (($validationRes = $this->validationMapping[$validationRule]($data)) !== -1) {
                if (isset($this->validationMessages[$field][$validationRes])) {
                    $errors[] = $this->validationMessages[$field][$validationRes];
                } else {
                    if (!isset($this->validationMessages[$validationRule][$validationRes])) {
                        throw new ValidatorException('Zpráva pro validované pole nebyla definována.');
                    }
                    $errors[] = $this->validationMessages[$validationRule][$validationRes];
                }
            }
        }

        return $errors;
    }
}