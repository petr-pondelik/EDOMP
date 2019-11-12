<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 8.11.19
 * Time: 13:25
 */

namespace App\TeacherModule\DI;

use App\CoreModule\DI\ModuleExtension;
use App\TeacherModule\Components\DataGrids\CategoryGridFactory;
use App\TeacherModule\Components\DataGrids\GroupGridFactory;
use App\TeacherModule\Components\DataGrids\LogoGridFactory;
use App\TeacherModule\Components\DataGrids\ProblemGridFactory;
use App\TeacherModule\Components\DataGrids\ProblemTypeGridFactory;
use App\TeacherModule\Components\DataGrids\SubCategoryGridFactory;
use App\TeacherModule\Components\DataGrids\SuperGroupGridFactory;
use App\TeacherModule\Components\DataGrids\TemplateGridFactory;
use App\TeacherModule\Components\DataGrids\TestGridFactory;
use App\TeacherModule\Components\DataGrids\UserGridFactory;
use App\TeacherModule\Components\FilterView\IFilterViewFactory;
use App\TeacherModule\Components\Forms\CategoryForm\ICategoryFormFactory;
use App\TeacherModule\Components\Forms\GroupForm\IGroupFormFactory;
use App\TeacherModule\Components\Forms\LogoForm\ILogoFormFactory;
use App\TeacherModule\Components\Forms\PermissionForm\IPermissionFormFactory;
use App\TeacherModule\Components\Forms\ProblemFinalForm\IProblemFinalFormFactory;
use App\TeacherModule\Components\Forms\ProblemTemplateForm\ArithmeticSeqTemplateForm\IArithmeticSeqTemplateFormFactory;
use App\TeacherModule\Components\Forms\ProblemTemplateForm\GeometricSeqTemplateForm\IGeometricSeqTemplateFormFactory;
use App\TeacherModule\Components\Forms\ProblemTemplateForm\LinearEqTemplateForm\ILinearEqTemplateFormFactory;
use App\TeacherModule\Components\Forms\ProblemTemplateForm\QuadraticEqTemplateForm\IQuadraticEqTemplateFormFactory;
use App\TeacherModule\Components\Forms\SubCategoryForm\ISubCategoryFormFactory;
use App\TeacherModule\Components\Forms\SuperGroupForm\ISuperGroupIFormFactory;
use App\TeacherModule\Components\Forms\TestForm\ITestFormFactory;
use App\TeacherModule\Components\Forms\TestTemplateForm\ITestTemplateFormFactory;
use App\TeacherModule\Components\Forms\UserForm\IUserFormFactory;
use App\TeacherModule\Components\LogoDragAndDrop\ILogoDragAndDropFactory;
use App\TeacherModule\Components\LogoView\ILogoViewFactory;
use App\TeacherModule\Components\ProblemStack\IProblemStackFactory;
use App\TeacherModule\Helpers\FilterViewHelper;
use App\TeacherModule\Helpers\NewtonParser;
use App\TeacherModule\Helpers\TestGeneratorHelper;
use App\TeacherModule\Plugins\ArithmeticSequencePlugin;
use App\TeacherModule\Plugins\GeometricSequencePlugin;
use App\TeacherModule\Plugins\LinearEquationPlugin;
use App\TeacherModule\Plugins\QuadraticEquationPlugin;
use App\TeacherModule\Services\ConditionService;
use App\TeacherModule\Services\FilterSession;
use App\TeacherModule\Services\MathService;
use App\TeacherModule\Services\NewtonApiClient;
use App\TeacherModule\Services\PluginContainer;
use App\TeacherModule\Services\ProblemDuplicity;
use App\TeacherModule\Services\ProblemGenerator;
use App\TeacherModule\Services\ProblemTemplateSession;
use App\TeacherModule\Services\TestGenerator;
use App\TeacherModule\Services\VariableFractionService;
use jlawrence\eos\Parser;
use Nette\DI\ContainerBuilder;

/**
 * Class App\TeacherModuleExtension
 * @package App\TeacherModule\DI
 */
class TeacherModuleExtension extends ModuleExtension
{
    /**
     * @var array
     */
    protected $defaults = [
        'newtonApiHost' => ''
    ];

    /**
     * @var array
     */
    protected $configRequiredItems = [ 'newtonApiHost' ];

    /**
     * @param ContainerBuilder $builder
     */
    public function addDefinitions(ContainerBuilder $builder): void
    {
        // Services definitions

        $builder->addDefinition($this->prefix('eosParser'))
            ->setType(Parser::class);

        $builder->addDefinition($this->prefix('newtonApiClient'))
            ->setType(NewtonApiClient::class)
            ->setArguments([
                'newtonApiHost' => $this->config['newtonApiHost']
            ]);

        $builder->addDefinition($this->prefix('mathService'))
            ->setType(MathService::class);

        $builder->addDefinition($this->prefix('conditionService'))
            ->setType(ConditionService::class);

        $builder->addDefinition($this->prefix('pluginContainer'))
            ->setType(PluginContainer::class);

        $builder->addDefinition($this->prefix('problemGenerator'))
            ->setType(ProblemGenerator::class);

        $builder->addDefinition($this->prefix('testGenerator'))
            ->setType(TestGenerator::class);

        $builder->addDefinition($this->prefix('problemDuplicity'))
            ->setType(	ProblemDuplicity::class);

        $builder->addDefinition($this->prefix('variableFraction'))
            ->setType(VariableFractionService::class);

        $builder->addDefinition($this->prefix('problemTemplateSession'))
            ->setType(ProblemTemplateSession::class);

        $builder->addDefinition($this->prefix('filterSession'))
            ->setType(FilterSession::class);

        // Problem Plugins definitions

        $builder->addDefinition($this->prefix('linearEquationPlugin'))
            ->setType(LinearEquationPlugin::class);

        $builder->addDefinition($this->prefix('quadraticEquationPlugin'))
            ->setType(QuadraticEquationPlugin::class);

        $builder->addDefinition($this->prefix('arithmeticSequencePlugin'))
            ->setType(ArithmeticSequencePlugin::class);

        $builder->addDefinition($this->prefix('geometricSequencePlugin'))
            ->setType(GeometricSequencePlugin::class);


        // Helpers definitions

        $builder->addDefinition($this->prefix('filterViewHelper'))
            ->setType(FilterViewHelper::class);

        $builder->addDefinition($this->prefix('newtonParser'))
            ->setType(NewtonParser::class);

        $builder->addDefinition($this->prefix('testGeneratorHelper'))
            ->setType(TestGeneratorHelper::class);


        // Factories definitions

        $builder->addDefinition($this->prefix('linearEquationTemplateFormFactory'))
            ->setImplement(ILinearEqTemplateFormFactory::class);

        $builder->addDefinition($this->prefix('quadraticEquationTemplateFormFactory'))
            ->setImplement(IQuadraticEqTemplateFormFactory::class);

        $builder->addDefinition($this->prefix('arithmeticSequenceTemplateFormFactory'))
            ->setImplement(	IArithmeticSeqTemplateFormFactory::class);

        $builder->addDefinition($this->prefix('geometricSequenceTemplateFormFactory'))
            ->setImplement(IGeometricSeqTemplateFormFactory::class);

        $builder->addDefinition($this->prefix('userFormFactory'))
            ->setImplement(IUserFormFactory::class);

        $builder->addDefinition($this->prefix('categoryFormFactory'))
            ->setImplement(ICategoryFormFactory::class);

        $builder->addDefinition($this->prefix('subCategoryFormFactory'))
            ->setImplement(ISubCategoryFormFactory::class);

        $builder->addDefinition($this->prefix('problemFinalFormFactory'))
            ->setImplement(IProblemFinalFormFactory::class);

        $builder->addDefinition($this->prefix('superGroupIFormFactory'))
            ->setImplement(ISuperGroupIFormFactory::class);

        $builder->addDefinition($this->prefix('logoIFormFactory'))
            ->setImplement(ILogoFormFactory::class);

        $builder->addDefinition($this->prefix('groupIFormFactory'))
            ->setImplement(IGroupFormFactory::class);

        $builder->addDefinition($this->prefix('testFormFactory'))
            ->setImplement(ITestFormFactory::class);

        $builder->addDefinition($this->prefix('permissionIFormFactory'))
            ->setImplement(IPermissionFormFactory::class);

        $builder->addDefinition($this->prefix('UserGridFactory'))
            ->setType(UserGridFactory::class);

        $builder->addDefinition($this->prefix('categoryGridFactory'))
            ->setType(CategoryGridFactory::class);

        $builder->addDefinition($this->prefix('subCategoryGridFactory'))
            ->setType(SubCategoryGridFactory::class);

        $builder->addDefinition($this->prefix('problemGridFactory'))
            ->setType(ProblemGridFactory::class);

        $builder->addDefinition($this->prefix('problemTypeGridFactory'))
            ->setType(ProblemTypeGridFactory::class);

        $builder->addDefinition($this->prefix('templateGridFactory'))
            ->setType(TemplateGridFactory::class);

        $builder->addDefinition($this->prefix('logoGridFactory'))
            ->setType(LogoGridFactory::class);

        $builder->addDefinition($this->prefix('superGroupGridFactory'))
            ->setType(SuperGroupGridFactory::class);

        $builder->addDefinition($this->prefix('groupGridFactory'))
            ->setType(GroupGridFactory::class);

        $builder->addDefinition($this->prefix('testGridFactory'))
            ->setType(TestGridFactory::class);

        $builder->addDefinition($this->prefix('logoDragAndDropFactory'))
            ->setImplement(ILogoDragAndDropFactory::class);

        $builder->addDefinition($this->prefix('problemStackFactory'))
            ->setImplement(IProblemStackFactory::class);

        $builder->addDefinition($this->prefix('logoViewFactory'))
            ->setImplement(ILogoViewFactory::class);

        $builder->addDefinition($this->prefix('filterViewFactory'))
            ->setImplement(IFilterViewFactory::class);

        $builder->addDefinition($this->prefix('testTemplateFormFactory'))
            ->setImplement(ITestTemplateFormFactory::class);
    }
}