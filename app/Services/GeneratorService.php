<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 6.2.19
 * Time: 23:53
 */

namespace App\Services;

use App\Helpers\ConstHelper;
use App\Helpers\StringsHelper;
use App\Model\Entity\ProblemTemplate;
use App\Model\Repository\ProblemTemplateRepository;
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
    protected $conditionsHelper;

    /**
     * @var array
     */
    private $generatorMarksMapping;

    /**
     * @var array
     */
    private $generatorAttrMapping;

    /**
     * @var array
     */
    protected $conditionsMatches;

    /**
     * GeneratorService constructor.
     * @param ProblemTemplateRepository $problemTemplateRepository
     * @param StringsHelper $stringsHelper
     * @param ConstHelper $constHelper
     */
    public function __construct
    (
        ProblemTemplateRepository $problemTemplateRepository,
        StringsHelper $stringsHelper, ConstHelper $constHelper
    )
    {
        $this->problemTemplateRepository = $problemTemplateRepository;
        $this->stringsHelper = $stringsHelper;
        $this->conditionsHelper = $constHelper;

        $this->generatorMarksMapping = [
            'integer' => 'integer',
            'float' => 'float'
        ];

        $this->generatorAttrMapping = [
            'type' => 'type',
            'min' => 'min',
            'max' => 'max'
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
        if($type === null) { return $this->generateInteger($min, $max); }
        if($this->generatorMarksMapping['integer'] === $type) { return $this->generateInteger($min, $max); }
        if($this->generatorMarksMapping['float'] === $type) { return $this->generateFloat($min, $max); }
        return false;
    }

    /**
     * @param $min
     * @param $max
     * @return int
     */
    public function generateInteger($min, $max): int
    {
        if(isset($min, $max)) { return mt_rand($min, $max); }
        if($min !== null) { return mt_rand($min, PHP_INT_MAX); }
        if($max !== null) { return mt_rand(0, $max); }
        return mt_rand();
    }

    /**
     * @param $min
     * @param $max
     * @return float|int
     */
    public function generateFloat($min, $max): int
    {
        if(isset($min, $max)){ return mt_rand($min*10, $max*10)/10; }
        if(isset($min)){ return mt_rand($min*10, PHP_INT_MAX)/10; }
        if(isset($max)){ return mt_rand(0, $max*10)/10; }
        return mt_rand()/10;
    }

    /**
     * @param String $xmpPar
     * @param String $attr
     * @return string|null
     */
    protected function getParAttr(String $xmpPar, String $attr): ?string
    {
        $start = Strings::indexOf($xmpPar, $attr);
        if(!$start){
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
        return ' '.$this->generatePar($type, $min ?? null, $max ?? null);
    }

    /**
     * @param String $inputBlock
     * @return string
     */
    protected function processBlock(String $inputBlock): string
    {
        $processedBlock = Strings::trim($inputBlock);
        if(Strings::match($processedBlock, '~(<par.*\/>)~')){
            $processedBlock = $this->processPar($processedBlock);
        }
        return $processedBlock;
    }

    /**
     * Process input problem prototype. Find parameters for generating, replace them with generated numbers and return final string.
     * @param string $expression
     * @return array
     */
    protected function generateParams(string $expression): array
    {
        $expressionSplit = $this->stringsHelper::splitByParameters($expression);

        $parameters = [];
        $paramsCnt = 0;

        //Check if split item is parameter. If true, trim this item and generate corresponding value.
        foreach($expressionSplit as $splitKey => $splitItem){
            $expressionSplit[$splitKey] = $this->processBlock($splitItem);
            if($splitItem !== ''){
                if(Strings::match($splitItem, '~(<par min="[0-9]+" max="[0-9]+"\/>)~')){
                    $parameters['p'.$paramsCnt++] = Strings::trim($expressionSplit[$splitKey]);
                }
            }
        }

        return $parameters;
    }

    /**
     * @param ProblemTemplate $problemTemplate
     * @return string
     * @throws \Nette\Utils\JsonException
     */
    public function generateProblemFinal(ProblemTemplate $problemTemplate): string
    {
        $parametrized = $this->stringsHelper::getParametrized($problemTemplate->getBody());

        // Use JSON matches array of problemPrototype
        $matchesJson = $this->problemTemplateRepository->find($problemTemplate->getId())->getMatches();
        $matchesArr = null;

        if($matchesJson){
            // Generate params matching the conditions
            $matchesArr = Json::decode($matchesJsons, Json::FORCE_ARRAY);
            $matchesCnt = count($matchesArr);
            $params = $matchesArr[$this->generateInteger(0, $matchesCnt - 1)];
        }
        else{
            // Generate params without conditions
            $params = $this->generateParams($problemTemplate->getBody());
        }

        return $this->stringsHelper::passValues($parametrized->expression, $params);
    }
}