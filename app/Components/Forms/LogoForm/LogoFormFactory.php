<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.5.19
 * Time: 11:55
 */

namespace App\Components\Forms\LogoForm;


use App\Components\Forms\BaseFormFactory;
use App\Model\Functionality\LogoFunctionality;
use App\Services\FileService;
use App\Services\ValidationService;

/**
 * Class LogoFormFactory
 * @package App\Components\Forms\LogoForm
 */
class LogoFormFactory extends BaseFormFactory
{
    /**
     * @var FileService
     */
    protected $fileService;

    /**
     * LogoFormFactory constructor.
     * @param ValidationService $validationService
     * @param LogoFunctionality $logoFunctionality
     * @param FileService $fileService
     */
    public function __construct
    (
        ValidationService $validationService, LogoFunctionality $logoFunctionality, FileService $fileService
    )
    {
        parent::__construct($validationService);
        $this->functionality = $logoFunctionality;
        $this->fileService = $fileService;
    }

    /**
     * @param bool $edit
     * @return LogoFormControl
     */
    public function create(bool $edit = false): LogoFormControl
    {
        return new LogoFormControl($this->validationService, $this->functionality, $this->fileService, $edit);
    }
}