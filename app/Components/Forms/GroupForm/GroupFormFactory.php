<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.5.19
 * Time: 19:54
 */

namespace App\Components\Forms\GroupForm;


use App\Components\Forms\BaseFormFactory;
use App\Model\Functionality\GroupFunctionality;
use App\Model\Repository\SuperGroupRepository;
use App\Services\ValidationService;

/**
 * Class GroupFormFactory
 * @package App\Components\Forms\GroupForm
 */
class GroupFormFactory extends BaseFormFactory
{
    /**
     * @var SuperGroupRepository
     */
    protected $superGroupRepository;

    /**
     * GroupFormFactory constructor.
     * @param ValidationService $validationService
     * @param GroupFunctionality $groupFunctionality
     * @param SuperGroupRepository $superGroupRepository
     */
    public function __construct
    (
        ValidationService $validationService, GroupFunctionality $groupFunctionality,
        SuperGroupRepository $superGroupRepository
    )
    {
        parent::__construct($validationService);
        $this->functionality = $groupFunctionality;
        $this->superGroupRepository = $superGroupRepository;
    }

    /**
     * @param bool $edit
     * @return GroupFormControl
     */
    public function create(bool $edit = false): GroupFormControl
    {
        return new GroupFormControl($this->validationService, $this->functionality, $this->superGroupRepository, $edit);
    }
}