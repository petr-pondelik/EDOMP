<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 6.2.19
 * Time: 23:53
 */

namespace App\Services;

use App\Exceptions\GeneratorException;
use App\Exceptions\ProblemDuplicityException;
use App\Helpers\ConstHelper;
use App\Helpers\RegularExpressions;
use App\Helpers\StringsHelper;
use App\Model\Persistent\Entity\ProblemTemplate\ProblemTemplate;
use App\Model\Persistent\Repository\ProblemTemplate\ProblemTemplateRepository;
use Nette\Utils\Json;
use Nette\Utils\Strings;

/**
 * Class GeneratorService
 * @package App\Helpers
 */
class GeneratorService
{
    /**
     * @var ProblemTemplateRepository
     */
    protected $problemTemplateRepository;

    /**
     * @var StringsHelper
     */
    protected $stringsHelper;

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * @var RegularExpressions
     */
    protected $regularExpressions;

    /**
     * @var array
     */
    protected $generatorMarksMapping;

    /**
     * @var array
     */
    protected $conditionsMatches;

    /**
     * GeneratorService constructor.
     * @param ProblemTemplateRepository $problemTemplateRepository
     * @param StringsHelper $stringsHelper
     * @param ConstHelper $constHelper
     * @param RegularExpressions $regularExpressions
     */
    public function __construct
    (
        ProblemTemplateRepository $problemTemplateRepository,
        StringsHelper $stringsHelper,
        ConstHelper $constHelper,
        RegularExpressions $regularExpressions
    )
    {
        $this->problemTemplateRepository = $problemTemplateRepository;
        $this->stringsHelper = $stringsHelper;
        $this->constHelper = $constHelper;
        $this->regularExpressions = $regularExpressions;

        $this->generatorMarksMapping = [
            'integer' => 'integer',
            'float' => 'float'
        ];
    }

    /**
     * @param String|null $type
     * @param int|null $min
     * @param int|null $max
     * @return bool|float|int
     */
    protected function generatePar(String $type = null, int $min = null, int $max = null)
    {
        if ($type === null) {
            return $this->generateInteger($min, $max);
        }
        if ($this->generatorMarksMapping['integer'] === $type) {
            return $this->generateInteger($min, $max);
        }
        if ($this->generatorMarksMapping['float'] === $type) {
            return $this->generateFloat($min, $max);
        }
        return false;
    }

    /**
     * @param $min
     * @param $max
     * @return int
     */
    public function generateInteger($min, $max): int
    {
        if (isset($min, $max)) {
            return mt_rand($min, $max);
        }
        if ($min !== null) {
            return mt_rand($min, PHP_INT_MAX);
        }
        if ($max !== null) {
            return mt_rand(0, $max);
        }
        return mt_rand();
    }

    /**
     * @param int $min
     * @param int $max
     * @param array|null $without
     * @return int|null
     */
    public function generateIntegerWithout(int $min, int $max, array $without = null): ?int
    {
        if (!isset($min, $max) || $min > $max) {
            return null;
        }

        if (!$without) {
            return $this->generateInteger($min, $max);
        }

        $res = null;
        $used = [];

        do {
            $res = $this->generateInteger($min, $max);
            $used[$res] = $res;
            if (count($used) > ($max - $min + 1)) {
                return null;
            }
        } while (in_array($res, $without, true));

        return $res;
    }

    /**
     * @param int $len
     * @return array|null
     */
    public function generateArrayUnique(int $len): ?array
    {
        $res = [];
        for ($i = 0; $i < $len; $i++) {
            $tmp = $this->generateIntegerWithout(0, $len - 1, $res);
            if ($tmp === null) {
                return null;
            }
            $res[] = $tmp;
        }
        return $res;
    }

    /**
     * @param $min
     * @param $max
     * @return float|int
     */
    public function generateFloat($min, $max): int
    {
        if (isset($min, $max)) {
            return mt_rand($min * 10, $max * 10) / 10;
        }
        if (isset($min)) {
            return mt_rand($min * 10, PHP_INT_MAX) / 10;
        }
        if (isset($max)) {
            return mt_rand(0, $max * 10) / 10;
        }
        return mt_rand() / 10;
    }

    /**
     * @param String $xmpPar
     * @param String $attr
     * @return string|null
     */
    protected function getParAttr(String $xmpPar, String $attr): ?string
    {
        $start = Strings::indexOf($xmpPar, $attr);
        if (!$start) {
            return null;
        }
        $xmpPar = Strings::substring($xmpPar, $start);
        $end = Strings::indexOf($xmpPar, '"', 2);
        return Strings::substring($xmpPar, Strings::indexOf($xmpPar, '"') + 1, $end - Strings::indexOf($xmpPar, '"') - 1);
    }

    /**
     * @param String $xmlPar
     * @return string
     */
    protected function processPar(String $xmlPar): string
    {
        $type = $this->getParAttr($xmlPar, 'type');
        $min = $this->getParAttr($xmlPar, 'min');
        $max = $this->getParAttr($xmlPar, 'max');
        return ' ' . $this->generatePar($type, $min ?? null, $max ?? null);
    }

    /**
     * @param String $inputBlock
     * @return string
     */
    protected function processBlock(String $inputBlock): string
    {
        $processedBlock = Strings::trim($inputBlock);
        if (Strings::match($processedBlock, '~(<par.*\/>)~')) {
            $processedBlock = $this->processPar($processedBlock);
        }
        return $processedBlock;
    }

    /**
     * Process input problem template. Find parameters for generating, replace them with generated numbers and return final string.
     * @param string $expression
     * @return array
     */
    protected function generateParams(string $expression): array
    {
        $expressionSplit = $this->stringsHelper::splitByParameters($expression);
        bdump($expressionSplit);

        $parameters = [];
        $paramsCnt = 0;

        //Check if split item is parameter. If true, trim this item and generate corresponding value.
        foreach ($expressionSplit as $splitKey => $splitItem) {
            $expressionSplit[$splitKey] = $this->processBlock($splitItem);
            if ($splitItem !== '') {

                if (Strings::match($splitItem, '~' . $this->regularExpressions::RE_PARAMETER_VALID . '~')) {
                    $parameters['p' . $paramsCnt++] = Strings::trim($expressionSplit[$splitKey]);
                }

//                if(Strings::match($splitItem, '~(<par min="[0-9]+" max="[0-9]+"\/>)~')){
//                    $parameters['p' . $paramsCnt++] = Strings::trim($expressionSplit[$splitKey]);
//                }


            }
        }

        return $parameters;
    }

    /**
     * @param ProblemTemplate $problemTemplate
     * @param array|null $usedMatchesInx
     * @return array
     * @throws GeneratorException
     * @throws \Nette\Utils\JsonException
     */
    public function generateProblemFinalBody(ProblemTemplate $problemTemplate, ?array $usedMatchesInx): array
    {
        bdump('GENERATE PROBLEM FINAL BODY');
        bdump($usedMatchesInx);

        $parametrized = $this->stringsHelper::getParametrized($problemTemplate->getBody());

        // Use JSON matches array of problemPrototype
        $matchesJson = $this->problemTemplateRepository->find($problemTemplate->getId())->getMatches();
        $matchesArr = null;
        $matchesIndex = null;

        if ($matchesJson) {
            // Generate params matching the conditions
            $matchesArr = Json::decode($matchesJson, Json::FORCE_ARRAY);
            $matchesCnt = count($matchesArr);

            $matchesIndex = $this->generateIntegerWithout(0, $matchesCnt - 1, $usedMatchesInx);

            if ($matchesIndex === null) {
                throw new GeneratorException("Can't generate problem final body - matchesArr exhausted.", false);
            }

            $params = $matchesArr[$matchesIndex];
        } else {
            // Generate params without conditions
            bdump($problemTemplate->getBody());
            $params = $this->generateParams($problemTemplate->getBody());
        }

        bdump('GENERATED PARAMETERS');
        bdump($params);

        return [$this->stringsHelper::passValues($parametrized->expression, $params), $matchesIndex];
    }
}