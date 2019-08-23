<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 23.5.19
 * Time: 19:54
 */

namespace App\Components\Forms\GroupForm;


use App\Components\Forms\FormFactory;
use App\Model\Persistent\Functionality\GroupFunctionality;
use App\Model\Persistent\Repository\SuperGroupRepository;
use App\Services\Validator;

/**
 * Class GroupFormFactory
 * @package App\Components\Forms\GroupForm
 */
class GroupFormFactory extends FormFactory
{
    /**
     * @var SuperGroupRepository
     */
    protected $superGroupRepository;

    /**
     * GroupFormFactory constructor.
     * @param Validator $validator
     * @param GroupFunctionality $groupFunctionality
     * @param SuperGroupRepository $superGroupRepository
     */
    public function __construct
    (
        Validator $validator, GroupFunctionality $groupFunctionality,
        SuperGroupRepository $superGroupRepository
    )
    {
        parent::__construct($validator);
        $this->functionality = $groupFunctionality;
        $this->superGroupRepository = $superGroupRepository;
    }

    /**
     * @param bool $edit
     * @return GroupFormControl
     */
    public function create(bool $edit = false): GroupFormControl
    {
        return new GroupFormControl($this->validator, $this->functionality, $this->superGroupRepository, $edit);
    }
}