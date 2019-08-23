<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.5.19
 * Time: 9:45
 */

namespace App\Components\Forms\TestStatisticsForm;


use App\Components\Forms\FormFactory;
use App\Model\Persistent\Functionality\ProblemFunctionality;
use App\Model\Persistent\Functionality\ProblemFinalTestVariantAssociationFunctionality;
use App\Model\Persistent\Repository\ProblemFinalTestVariantAssociationRepository;
use App\Model\Persistent\Repository\TestRepository;
use App\Services\Validator;

/**
 * Class TestStatisticsFormFactory
 * @package App\Components\Forms\TestStatisticsForm
 */
class TestStatisticsFormFactory extends FormFactory
{
    /**
     * @var TestRepository
     */
    protected $testRepository;

    /**
     * @var ProblemFinalTestVariantAssociationFunctionality
     */
    protected $problemFinalTestVariantAssociationFunctionality;


    public function __construct
    (
        Validator $validator,
        ProblemFunctionality $problemFunctionality,
        ProblemFinalTestVariantAssociationFunctionality $problemFinalTestVariantAssociationFunctionality,
        TestRepository $testRepository
    )
    {
        parent::__construct($validator);
        $this->functionality = $problemFunctionality;
        $this->problemFinalTestVariantAssociationFunctionality = $problemFinalTestVariantAssociationFunctionality;
        $this->testRepository = $testRepository;
    }

    /**
     * @param int $testId
     * @return TestStatisticsFormControl
     */
    public function create(int $testId): TestStatisticsFormControl
    {
        return new TestStatisticsFormControl(
            $this->validator, $this->functionality,
            $this->problemFinalTestVariantAssociationFunctionality, $this->testRepository, $testId
        );
    }
}