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
use App\TeacherModule\Services\LatexParser;
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
final class Validator
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
     * @var LatexParser
     */
    protected $latexParser;

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
        0 => 'T??lo ??lohy mus?? b??t vypln??no.',
        1 => 'Vstupn?? LaTeX mus?? b??t uvnit?? zna??ek pro matematick?? m??d.',
        2 => '??ablona neobsahuje zadanou nezn??mou.',
        3 => '??ablona nen?? validn?? matematick?? v??raz.'
    ];

    /**
     * Validator constructor.
     * @param NewtonApiClient $newtonApiClient
     * @param UserRepository $userRepository
     * @param TemplateJsonDataFunctionality $templateJsonDataFunctionality
     * @param ConstHelper $constHelper
     * @param StringsHelper $stringsHelper
     * @param LatexParser $latexParser
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
        ConstHelper $constHelper, StringsHelper $stringsHelper, LatexParser $latexParser,
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
        $this->latexParser = $latexParser;
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
                $match = Strings::match($filledVal, '~' . $regularExpressions::RE_SCHOOL_YEAR . '~');
                if ($match[0] !== $filledVal) {
                    return 1;
                }
                [$first, $second] = [$match[1], $match[3]];
                $first = (int) Strings::substring($first, 2, 2);
                if (strlen($second) > 2) {
                    $second = (int) Strings::substring($second, 2, 2);
                } else {
                    $second = (int) $second;
                }
                if ($first >= $second) {
                    return 2;
                }
                if (($second - $first) > 1) {
                    return 3;
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
                return $this->quadraticEquationPlugin->validateBody($problemTemplate);
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
                0 => 'Zadejte e-mail',
                1 => 'E-mail nesm?? b??t del???? ne?? 128 znak??',
                2 => 'Zadejte validn?? e-mail'
            ],

            'username' => [
                0 => 'U??ivatelsk?? jm??no nesm?? b??t del???? ne?? 128 znak??',
            ],

            'login' => [
                0 => 'Zadejte e-mail ??i u??ivatelsk?? jm??no',
                1 => '??daj nesm?? b??t del???? ne?? 128 znak??'
            ],

            'password' => [
                0 => 'Zadejte heslo'
            ],

            'passwordConfirm' => [
                0 => 'Ob?? hesla mus?? b??t vypln??na a shodovat se.',
                1 => 'Heslo mus?? m??t d??lku alespo?? 8 znak??.'
            ],

            'groups' => [
                0 => 'Zvolte alespo?? jednu skupinu'
            ],

            'label' => [
                0 => 'N??zev mus?? b??t vypln??n',
                1 => 'N??zev nesm?? b??t del???? ne?? 128 znak??'
            ],

            'logo' => [
                0 => 'Zvolte pros??m logo.'
            ],

            'schoolYear' => [
                0 => '??koln?? rok mus?? b?? vypln??n.',
                1 => '??koln?? roku mus?? b??t ve form??tu rrrr/rr(rr) nebo rrrr-rr(rr).',
                2 => 'Rok n??sleduj??c?? po p??elomu mus?? b??t v??t???? ne?? rok p??edch??zej??c??.',
                3 => 'Rok po p??elomu mus?? p????mo n??sledovat roku p??edchoz??mu.'
            ],

            'testNumber' => [
                0 => '????slo testu mus?? b??t vypln??no.',
                1 => '????slo testu mus?? b??t cel?? ????slo.',
                2 => '????slo testu nesm?? b??t z??porn??.'
            ],

            'term' => [
                0 => 'Obdob?? testu mus?? b??t vypln??no.'
            ],

            'successRate' => [
                0 => '??sp????nost ??lohy mus?? b??t ????seln?? hodnota.',
                1 => '??sp????nost ??lohy mus?? b??t v intervalu <0; 1>.'
            ],

            'body' => self::$bodyMessages,
            'body_' . $this->constHelper::LINEAR_EQ => self::$bodyMessages,
            'body_' . $this->constHelper::QUADRATIC_EQ => self::$bodyMessages,
            'body_' . $this->constHelper::ARITHMETIC_SEQ => self::$bodyMessages,
            'body_' . $this->constHelper::GEOMETRIC_SEQ => self::$bodyMessages,

            'variable' => [
                0 => 'Zadejte pros??m nezn??mou.',
                1 => 'Zadejte pros??m pr??v?? jedno mal?? p??smo abecedy.',
                2 => 'Prom??nn?? nesm?? b??t symbol e.'
            ],

            'firstN' => [
                0 => 'Zvolte po??et prvn??ch ??len??.',
                1 => 'Po??et prvn??ch ??len?? mus?? b??t kladn??.'
            ],

            'theme' => [
                0 => 'Zvolte pros??m t??ma.'
            ],

            'subTheme' => [
                0 => 'Zvolte pros??m podt??ma.'
            ],

            'difficulty' => [
                0 => 'Zvolte pros??m obt????nost.'
            ],

            'problemType' => [
                0 => 'Zvolte pros??m typ ??lohy.'
            ],

            'type_' . $this->constHelper::LINEAR_EQ => [
                0 => 'Zvolte pros??m typ ??lohy.',
                1 => 'Zadan?? ??loha nen?? line??rn?? rovnic??.'
            ],

            'type_' . $this->constHelper::QUADRATIC_EQ => [
                0 => 'Zvolte pros??m typ ??lohy.',
                1 => 'Zadan?? ??loha nen?? kvadratickou rovnic??.'
            ],

            'type_' . $this->constHelper::ARITHMETIC_SEQ => [
                0 => 'Zvolte pros??m typ ??lohy.',
                1 => 'Zadan?? ??loha nen?? aritmetickou posloupnost??.'
            ],

            'type_' . $this->constHelper::GEOMETRIC_SEQ => [
                0 => 'Zvolte pros??m typ ??lohy.',
                1 => 'Zadan?? ??loha nen?? geometrickou posloupnost??.'
            ],

            'condition_' . $this->constHelper::RESULT => [
                0 => 'Struktura mus?? b??t vypln??na',
                1 => 'Chybn?? form??t vstupn??ho LaTeXu.',
                2 => 'P??ekro??en povolen?? po??et parametr??. (maxim??ln?? ' . $this->constHelper::PARAMETERS_MAX . ')-',
                3 => 'P??ekro??ena povolen?? slo??itost parametr??. (maxim??ln?? ' . $this->constHelper::COMPLEXITY_MAX . ').',
                4 => 'Podm??nka nen?? splniteln??.'
            ],

            'condition_' . $this->constHelper::DISCRIMINANT => [
                0 => 'Struktura ??lohy mus?? b??t vypln??na.',
                1 => 'Chybn?? form??t vstupn??ho LaTeXu.',
                2 => 'P??ekro??en povolen?? po??et parametr??. (maxim??ln?? ' . $this->constHelper::PARAMETERS_MAX . ').',
                3 => 'P??ekro??ena povolen?? slo??itost parametr??. (maxim??ln?? ' . $this->constHelper::COMPLEXITY_MAX . ').',
                4 => 'Podm??nka nen?? splniteln??.',
            ],

            'role' => [
                0 => 'Zvolte roli',
            ],

            'superGroup' => [
                0 => 'Zvolte superskupinu',
            ],

            'templateContent' => [
                0 => 'Obsah ??ablony nesm?? b??t pr??zdn??',
                1 => 'PHP k??d ??ablony nesm?? b??t zm??n??n',
            ],

            'firstName' => [
                0 => 'Zadejte jm??no',
            ],

            'lastName' => [
                0 => 'Zadejte p????jmen??',
            ],

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
                throw new NotSupportedException('Valid??tor: Po??adavek obsahuje neo??ek??vanou hodnotu.');
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
                            throw new ValidatorException('Zpr??va pro validovan?? pole nebyla definov??na.');
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
                throw new NotSupportedException('Valid??tor: Po??adavek obsahuje neo??ek??vanou hodnotu.');
            }

            if (($validationRes = $this->validationMapping[$validationRule]($data)) !== -1) {
                if (isset($this->validationMessages[$field][$validationRes])) {
                    $errors[] = $this->validationMessages[$field][$validationRes];
                } else {
                    if (!isset($this->validationMessages[$validationRule][$validationRes])) {
                        throw new ValidatorException('Zpr??va pro validovan?? pole nebyla definov??na.');
                    }
                    $errors[] = $this->validationMessages[$validationRule][$validationRes];
                }
            }
        }

        return $errors;
    }
}