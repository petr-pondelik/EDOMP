<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 17:36
 */

namespace App\Service;

use App\Helpers\ConstHelper;
use App\Helpers\LatexHelper;
use App\Helpers\StringsHelper;
use Nette\NotSupportedException;
use Nette\Utils\Strings;

/**
 * Class ValidationService
 * @package App\Service
 */
class ValidationService
{
    /**
     * @var NewtonApiClient
     */
    protected $newtonApiClient;

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

    public function __construct
    (
        NewtonApiClient $newtonApiClient,
        ConstHelper $constHelper, StringsHelper $stringsHelper, LatexHelper $latexHelper
    )
    {
        $this->newtonApiClient = $newtonApiClient;
        $this->constHelper = $constHelper;
        $this->stringsHelper = $stringsHelper;
        $this->latexHelper = $latexHelper;

        $this->validationMapping = [

            "label" => function($filledVal){
                if(empty($filledVal)) return 0;
                return -1;
            },

            "body" => function($filledVal){
                return $this->validateBody($filledVal->body, $filledVal->bodyType);
            },

        ];

        $this->validationMessages = [

            "label" => [
                0 => "Název musí být vyplněn."
            ],

            "body" => [
                0 => "Tělo úlohy musí být vyplněno.",
                1 => "Vstupní LaTeX musí být uvnitř značek pro matematický mód.",
                2 => "Šablona neobsahuje zadanou neznámou.",
                3 => "Šablona není validní matematický výraz."
            ],

        ];
    }

    /**
     * @param string $body
     * @param int $type
     * @param string|null $variable
     * @return int
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function validateBody(string $body, int $type,  string $variable = null): int
    {
        if(empty($body)) return 0;

        if(!$this->latexHelper::latexWrapped($body)) return 1;

        if($type !== $this->constHelper::BODY_FINAL){

            $parsed = $this->latexHelper::parseLatex($body);
            bdump("PARSED: " . $parsed);
            $split = $this->stringsHelper::splitByParameters($parsed);

            if(empty($variable) || !$this->stringsHelper::containsVariable($split, $variable))
                return 2;

            $parametrized = $this->stringsHelper::getParametrized($parsed);

            bdump($parametrized);

            $parsedNewton = $this->stringsHelper::newtonFormat($parametrized->expression);
            $newtonApiRes = $this->newtonApiClient->simplify($parsedNewton);

            if(Strings::contains($newtonApiRes, "Stop"))
                return 3;
        }

        return -1;
    }

    /**
     * @param $fields
     * @return array
     */
    public function validate($fields)
    {
        $validationErrors = [];

        foreach((array)$fields as $key1 => $value1){

            if(!array_key_exists($key1, $this->validationMapping))
                throw new NotSupportedException("Požadavek obsahuje neočekávanou hodnotu.");

            if(is_array($value1)){
                foreach ($value1 as $key2 => $value2){
                    if(!array_key_exists($key2, $this->validationMapping[$key1]))
                        throw  new NotSupportedException("Požadavek obsahuje neočekávanou hodnotu.");
                    if( ($validationRes = $this->validationMapping[$key1][$key2]($value2)) !== -1 )
                        $validationErrors[$key1][] = $this->validationMessages[$key1][$key2][$validationRes];
                }
            }
            else{
                if( ($validationRes = $this->validationMapping[$key1]($value1)) !== -1 )
                    $validationErrors[$key1][] = $this->validationMessages[$key1][$validationRes];
            }
        }

        return $validationErrors;
    }

}