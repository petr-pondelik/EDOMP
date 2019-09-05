<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 17:36
 */

namespace App\Services;

use App\Helpers\ConstHelper;
use App\Helpers\LatexHelper;
use App\Helpers\RegularExpressions;
use App\Helpers\StringsHelper;
use App\Model\NonPersistent\Entity\ArithmeticSequenceTemplateNP;
use App\Model\NonPersistent\Entity\GeometricSequenceTemplateNP;
use App\Model\NonPersistent\Entity\LinearEquationTemplateNP;
use App\Model\NonPersistent\Entity\ProblemTemplateNP;
use App\Model\NonPersistent\Entity\QuadraticEquationTemplateNP;
use App\Model\Persistent\Functionality\TemplateJsonDataFunctionality;
use App\Model\Persistent\Repository\UserRepository;
use App\Plugins\ArithmeticSequencePlugin;
use App\Plugins\GeometricSequencePlugin;
use App\Plugins\LinearEquationPlugin;
use App\Plugins\QuadraticEquationPlugin;
use Nette\Application\UI\Form;
use Nette\NotSupportedException;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;

/**
 * Class Validator
 * @package App\Service
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
        UserRepository $userRepository, TemplateJsonDataFunctionality $templateJsonDataFunctionality,
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

            'notEmpty' => static function ($data) {
                //bdump($data);
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
                if (strlen($data) > 64) {
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

            'username' => function ($data) {
                if (empty($data->username)) {
                    return 0;
                }
                if (strlen($data->username) > 64) {
                    return 1;
                }
                if ($data->edit) {
                    $user = $this->userRepository->findOneBy(['username' => $data->username]);
                    if ($user->getId() !== (int)$data->userId) {
                        return 2;
                    }
                } else if ($this->userRepository->findOneBy(['username' => $data->username])) {
                    return 2;
                }
                return -1;
            },

            // Validate password in administration User section
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
                //bdump('VALIDATE LINEAR EQUATION BODY VALIDATOR');
                //bdump($problemTemplate);
                if(empty($problemTemplate->getBody()) || empty($problemTemplate->getVariable())){
                    return 0;
                }
                return $this->linearEquationPlugin->validateBody($problemTemplate);
            },

            'body_' . $this->constHelper::QUADRATIC_EQ => function (QuadraticEquationTemplateNP $problemTemplate) {
                if(empty($problemTemplate->getBody()) || empty($problemTemplate->getVariable())){
                    return 0;
                }
                return $this->linearEquationPlugin->validateBody($problemTemplate);
            },

            'body_' . $this->constHelper::ARITHMETIC_SEQ => function (ArithmeticSequenceTemplateNP $problemTemplate) {
                if(empty($problemTemplate->getBody()) || empty($problemTemplate->getVariable())){
                    return 0;
                }
                return $this->arithmeticSequencePlugin->validateBody($problemTemplate);
            },

            'body_' . $this->constHelper::GEOMETRIC_SEQ => function (GeometricSequenceTemplateNP $problemTemplate) {
                if(empty($problemTemplate->getBody()) || empty($problemTemplate->getVariable())){
                    return 0;
                }
                return $this->geometricSequencePlugin->validateBody($problemTemplate);
            },

            'variable' => static function ($value) {
                if (empty($value)) {
                    return 0;
                }
                $matches = Strings::match($value, '~^[a-z]$~');
                if (!$matches || strlen($value) !== 1 || count($matches) !== 1) {
                    return 1;
                }
                if($value === 'e'){
                    return 2;
                }
                return -1;
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

            'type_' . $this->constHelper::LINEAR_EQ => function (LinearEquationTemplateNP $problemTemplate) {
                if (!$problemTemplate) {
                    return 0;
                }
                //bdump($problemTemplate);
                if(!$this->linearEquationPlugin->validateType($problemTemplate)){
                    return 1;
                }
                return -1;
            },

            'type_' . $this->constHelper::QUADRATIC_EQ => function (QuadraticEquationTemplateNP $problemTemplate) {
                if (!$problemTemplate) {
                    return 0;
                }
                if (!$this->quadraticEquationPlugin->validateType($problemTemplate)) {
                    return 1;
                }
                return -1;
            },

            'type_' . $this->constHelper::ARITHMETIC_SEQ => function (ArithmeticSequenceTemplateNP $problemTemplate) {
                if (!$problemTemplate) {
                    return 0;
                }
                if(!$this->arithmeticSequencePlugin->validateType($problemTemplate)){
                    return 1;
                }
                return -1;
            },

            'type_' . $this->constHelper::GEOMETRIC_SEQ => function (GeometricSequenceTemplateNP $problemTemplate) {
                if (!$problemTemplate) {
                    return 0;
                }
                if(!$this->geometricSequencePlugin->validateType($problemTemplate)){
                    return 1;
                }
                return -1;
            },

            'condition_' . $this->constHelper::RESULT => function (ProblemTemplateNP $data) {
                //bdump('VALIDATE RESULT CONDITION');
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
                //bdump('VALIDATE DISCRIMINANT CONDITION');
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
        ];

        $this->validationMessages = [

            'username' => [
                0 => 'Zadejte uživatelské jméno.',
                1 => 'Uživatelské jméno nesmí být delší než 64 znaků.',
                2 => 'Zadané uživatelské jméno již existuje.'
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
                1 => 'Název nesmí být delší než 64 znaků.'
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
                1 => 'Úspěšnost úlohy musí být v intervalu <0; 1>'
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
                1 => 'Chybný formát vstupního LaTeXu',
                2 => 'Překročen povolený počet parametrů. (maximálně ' . $this->constHelper::PARAMETERS_MAX . ')',
                3 => 'Překročena povolená složitost parametrů. (maximálně ' . $this->constHelper::COMPLEXITY_MAX . ')',
                4 => 'Podmínka není splnitelná.'
            ],

            'condition_' . $this->constHelper::DISCRIMINANT => [
                0 => 'Struktura úlohy musí být vyplněna.',
                1 => 'Chybný formát vstupního LaTeXu.',
                2 => 'Překročen povolený počet parametrů. (maximálně ' . $this->constHelper::PARAMETERS_MAX . ')',
                3 => 'Překročena povolená složitost parametrů. (maximálně ' . $this->constHelper::COMPLEXITY_MAX . ')',
                4 => 'Podmínka není splnitelná.'
            ],

            'conditions_valid' => [
                0 => 'Některou ze zadaných podmínek nelze splnit.'
            ],

            'role' => [
                0 => 'Zvolte prosím roli.'
            ],

            'superGroup' => [
                0 => 'Zvolte prosím superskupinu.'
            ]

        ];
    }

    /**
     * @param Form $form
     * @param $fields
     * @return Form
     */
    public function validate(Form $form, $fields): Form
    {
        foreach ((array)$fields as $field => $item) {

            $validationRule = $item->validationRule;
            $data = $item->data;

            // Check if the validator supports entered validation
            if (!array_key_exists($validationRule, $this->validationMapping)) {
                throw new NotSupportedException('Požadavek obsahuje neočekávanou hodnotu.');
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
                        $form[$item->display]->addError($this->validationMessages[$validationRule][$validationRes]);
                    } else {
                        $form[$field]->addError($this->validationMessages[$validationRule][$validationRes]);
                    }
                }
            }
        }

        return $form;
    }

//    /**
//     * @param Form $form
//     * @param $fields
//     * @return Form
//     */
//    public function conditionValidate(Form $form, $fields): Form
//    {
//        // Based on the problemId presence, it will be decided it to update or to create
//        foreach ((array)$fields as $field => $item) {
//
//            $validationRule = $item->validationRule;
//            $data = $item->data;
//
//            // Check if the validator supports entered validation
//            if (!array_key_exists($validationRule, $this->validationMapping)) {
//                throw new NotSupportedException('Požadavek obsahuje neočekávanou hodnotu.');
//            }
//
//            if (($validationRes = $this->validationMapping[$validationRule]($data)) !== -1) { // Zde se liší!
//                if (isset($this->validationMessages[$field][$validationRes])) {
//                    if (isset($item->display)) {
//                        $form[$item->display]->addError($this->validationMessages[$field][$validationRes]);
//                    } else {
//                        $form[$field]->addError($this->validationMessages[$field][$validationRes]);
//                    }
//                } else {
//                    if (isset($item->display)) {
//                        $form[$item->display]->addError($this->validationMessages[$validationRule][$validationRes]);
//                    } else {
//                        $form[$field]->addError($this->validationMessages[$validationRule][$validationRes]);
//                    }
//                }
//            }
//
//        }
//
//        return $form;
//    }
}