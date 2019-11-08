<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 8.11.19
 * Time: 13:25
 */

namespace App\TeacherModule\DI;

use App\TeacherModule\Components\Forms\ProblemTemplateForm\ArithmeticSeqTemplateForm\IArithmeticSeqTemplateFormFactory;
use App\TeacherModule\Components\Forms\ProblemTemplateForm\GeometricSeqTemplateForm\IGeometricSeqTemplateFormFactory;
use App\TeacherModule\Components\Forms\ProblemTemplateForm\LinearEqTemplateForm\ILinearEqTemplateFormFactory;
use App\TeacherModule\Components\Forms\ProblemTemplateForm\QuadraticEqTemplateForm\IQuadraticEqTemplateFormFactory;
use App\TeacherModule\Plugins\ArithmeticSequencePlugin;
use App\TeacherModule\Plugins\GeometricSequencePlugin;
use App\TeacherModule\Plugins\LinearEquationPlugin;
use App\TeacherModule\Plugins\QuadraticEquationPlugin;
use App\TeacherModule\Services\ConditionService;
use App\TeacherModule\Services\MathService;
use App\TeacherModule\Services\NewtonApiClient;
use App\TeacherModule\Services\PluginContainer;
use App\TeacherModule\Services\ProblemDuplicity;
use App\TeacherModule\Services\ProblemGenerator;
use App\TeacherModule\Services\TestGenerator;
use App\TeacherModule\Services\VariableFractionService;
use Nette\DI\CompilerExtension;
use Nette\DI\ContainerBuilder;

/**
 * Class App\TeacherModuleExtension
 * @package App\TeacherModule\DI
 */
class TeacherModuleExtension extends CompilerExtension
{
    /**
     * @var array
     */
    protected $defaults = [
        'newtonApiHost' => ''
    ];

    public function loadConfiguration(): void
    {
        parent::loadConfiguration();

        $this->validateConfig($this->defaults);

        // Get DI container builder
        $builder = $this->getContainerBuilder();

        // Register Services into DI container
        $this->registerServices($builder);

        // Register Problem Plugins into DI container
        $this->registerProblemPlugins($builder);
    }

    /**
     * @param ContainerBuilder $builder
     */
    public function registerProblemPlugins(ContainerBuilder $builder): void
    {
        $builder->addDefinition($this->prefix('linearEquationPlugin'))
            ->setType(LinearEquationPlugin::class);

        $builder->addDefinition($this->prefix('quadraticEquationPlugin'))
            ->setType(QuadraticEquationPlugin::class);

        $builder->addDefinition($this->prefix('arithmeticSequencePlugin'))
            ->setType(ArithmeticSequencePlugin::class);

        $builder->addDefinition($this->prefix('geometricSequencePlugin'))
            ->setType(GeometricSequencePlugin::class);
    }

    /**
     * @param ContainerBuilder $builder
     */
    public function registerServices(ContainerBuilder $builder): void
    {
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
    }

    /**
     * @param ContainerBuilder $builder
     */
    public function registerFactories(ContainerBuilder $builder): void
    {
        $builder->addDefinition($this->prefix('linearEquationTemplateFormFactory'))
            ->setImplement(ILinearEqTemplateFormFactory::class);

        $builder->addDefinition($this->prefix('quadraticEquationTemplateFormFactory'))
            ->setImplement(IQuadraticEqTemplateFormFactory::class);

        $builder->addDefinition($this->prefix('arithmeticSequenceTemplateFormFactory'))
            ->setImplement(	IArithmeticSeqTemplateFormFactory::class);

        $builder->addDefinition($this->prefix('geometricSequenceTemplateFormFactory'))
            ->setImplement(IGeometricSeqTemplateFormFactory::class);
    }
}