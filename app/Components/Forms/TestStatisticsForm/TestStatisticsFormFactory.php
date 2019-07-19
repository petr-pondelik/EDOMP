<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.5.19
 * Time: 9:45
 */

namespace App\Components\Forms\TestStatisticsForm;


use App\Components\Forms\FormFactory;
use App\Model\Functionality\ProblemFunctionality;
use App\Model\Functionality\ProblemFinalTestVariantAssociationFunctionality;
use App\Model\Repository\ProblemFinalTestVariantAssociationRepository;
use App\Model\Repository\TestRepository;
use App\Services\ValidationService;

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
        ValidationService $validationService,
        ProblemFunctionality $problemFunctionality,
        ProblemFinalTestVariantAssociationFunctionality $problemFinalTestVariantAssociationFunctionality,
        TestRepository $testRepository
    )
    {
        parent::__construct($validationService);
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
            $this->validationService, $this->functionality,
            $this->problemFinalTestVariantAssociationFunctionality, $this->testRepository, $testId
        );
    }
}