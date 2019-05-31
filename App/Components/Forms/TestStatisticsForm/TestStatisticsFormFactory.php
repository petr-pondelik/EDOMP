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
use App\Model\Functionality\ProblemTestAssociationFunctionality;
use App\Model\Repository\ProblemTestAssociationRepository;
use App\Services\ValidationService;

/**
 * Class TestStatisticsFormFactory
 * @package App\Components\Forms\TestStatisticsForm
 */
class TestStatisticsFormFactory extends FormFactory
{
    /**
     * @var ProblemTestAssociationRepository
     */
    protected $problemTestAssociationRepository;

    /**
     * @var ProblemTestAssociationFunctionality
     */
    protected $problemTestAssociationFunctionality;

    /**
     * TestStatisticsFormFactory constructor.
     * @param ValidationService $validationService
     * @param ProblemFunctionality $problemFunctionality
     * @param ProblemTestAssociationRepository $problemTestAssociationRepository
     * @param ProblemTestAssociationFunctionality $problemTestAssociationFunctionality
     */
    public function __construct
    (
        ValidationService $validationService,
        ProblemFunctionality $problemFunctionality,
        ProblemTestAssociationRepository $problemTestAssociationRepository, ProblemTestAssociationFunctionality $problemTestAssociationFunctionality
    )
    {
        parent::__construct($validationService);
        $this->functionality = $problemFunctionality;
        $this->problemTestAssociationRepository = $problemTestAssociationRepository;
        $this->problemTestAssociationFunctionality = $problemTestAssociationFunctionality;
    }

    /**
     * @param int $testId
     * @return TestStatisticsFormControl
     */
    public function create(int $testId): TestStatisticsFormControl
    {
        return new TestStatisticsFormControl(
            $this->validationService, $this->functionality,
            $this->problemTestAssociationRepository, $this->problemTestAssociationFunctionality, $testId
        );
    }
}