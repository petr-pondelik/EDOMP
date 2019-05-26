<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.5.19
 * Time: 15:42
 */

namespace App\Components\Forms;

use App\Model\Functionality\BaseFunctionality;
use App\Services\ValidationService;

/**
 * Class BaseFormFactory
 * @package App\Components\Forms
 */
abstract class BaseFormFactory
{
    /**
     * @var ValidationService
     */
    protected $validationService;

    /**
     * @var BaseFunctionality
     */
    protected $functionality;

    /**
     * BaseFormFactory constructor.
     * @param ValidationService $validationService
     */
    public function __construct(ValidationService $validationService)
    {
        $this->validationService = $validationService;
    }
}