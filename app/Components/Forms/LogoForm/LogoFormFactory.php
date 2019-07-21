<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.5.19
 * Time: 11:55
 */

namespace App\Components\Forms\LogoForm;


use App\Components\Forms\FormFactory;
use App\Model\Functionality\LogoFunctionality;
use App\Services\FileService;
use App\Services\Validator;

/**
 * Class LogoFormFactory
 * @package App\Components\Forms\LogoForm
 */
class LogoFormFactory extends FormFactory
{
    /**
     * @var FileService
     */
    protected $fileService;

    /**
     * LogoFormFactory constructor.
     * @param Validator $validator
     * @param LogoFunctionality $logoFunctionality
     * @param FileService $fileService
     */
    public function __construct
    (
        Validator $validator, LogoFunctionality $logoFunctionality, FileService $fileService
    )
    {
        parent::__construct($validator);
        $this->functionality = $logoFunctionality;
        $this->fileService = $fileService;
    }

    /**
     * @param bool $edit
     * @return LogoFormControl
     */
    public function create(bool $edit = false): LogoFormControl
    {
        return new LogoFormControl($this->validator, $this->functionality, $this->fileService, $edit);
    }
}