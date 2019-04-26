<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.4.19
 * Time: 17:36
 */

namespace App\Service;

use Nette\NotSupportedException;

/**
 * Class ValidationService
 * @package App\Service
 */
class ValidationService
{

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

    )
    {
        $this->validationMapping = [

            "label" => function($filledVal){
                if(empty($filledVal)) return 0;
                return -1;
            },

        ];

        $this->validationMessages = [

            "label" => [
                0 => "Název musí být vyplněn."
            ],

        ];
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