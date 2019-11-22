<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 8.11.19
 * Time: 19:29
 */

namespace App\CoreModule\DI;


use App\CoreModule\Components\ForgetPassword\IForgetPasswordFactory;
use App\CoreModule\Components\Forms\ForgetPasswordForm\IForgetPasswordFormFactory;
use App\CoreModule\Components\Forms\PasswordForm\IPasswordFormFactory;
use App\CoreModule\Components\Forms\SignForm\ISignFormFactory;
use App\CoreModule\Components\HeaderBar\IHeaderBarFactory;
use App\CoreModule\Components\HelpModal\IHelpModalFactory;
use App\CoreModule\Components\SideBar\ISideBarFactory;
use App\CoreModule\Helpers\ConstHelper;
use App\CoreModule\Helpers\FlashesTranslator;
use App\CoreModule\Helpers\FormatterHelper;
use App\CoreModule\Helpers\RegularExpressions;
use App\CoreModule\Helpers\StringsHelper;
use App\CoreModule\Model\Persistent\Entity\Theme;
use App\CoreModule\Model\Persistent\Entity\Difficulty;
use App\CoreModule\Model\Persistent\Entity\Filter;
use App\CoreModule\Model\Persistent\Entity\Group;
use App\CoreModule\Model\Persistent\Entity\Logo;
use App\CoreModule\Model\Persistent\Entity\Problem;
use App\CoreModule\Model\Persistent\Entity\ProblemCondition;
use App\CoreModule\Model\Persistent\Entity\ProblemConditionType;
use App\CoreModule\Model\Persistent\Entity\ProblemFinal\ArithmeticSequenceFinal;
use App\CoreModule\Model\Persistent\Entity\ProblemFinal\GeometricSequenceFinal;
use App\CoreModule\Model\Persistent\Entity\ProblemFinal\LinearEquationFinal;
use App\CoreModule\Model\Persistent\Entity\ProblemFinal\ProblemFinal;
use App\CoreModule\Model\Persistent\Entity\ProblemFinal\QuadraticEquationFinal;
use App\CoreModule\Model\Persistent\Entity\ProblemFinalTestVariantAssociation;
use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\ArithmeticSequenceTemplate;
use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\GeometricSequenceTemplate;
use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\LinearEquationTemplate;
use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\ProblemTemplate;
use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\QuadraticEquationTemplate;
use App\CoreModule\Model\Persistent\Entity\ProblemType;
use App\CoreModule\Model\Persistent\Entity\Role;
use App\CoreModule\Model\Persistent\Entity\SubTheme;
use App\CoreModule\Model\Persistent\Entity\SuperGroup;
use App\CoreModule\Model\Persistent\Entity\TemplateJsonData;
use App\CoreModule\Model\Persistent\Entity\Test;
use App\CoreModule\Model\Persistent\Entity\TestVariant;
use App\CoreModule\Model\Persistent\Entity\User;
use App\CoreModule\Model\Persistent\Entity\ValidationFunction;
use App\CoreModule\Model\Persistent\Functionality\ThemeFunctionality;
use App\CoreModule\Model\Persistent\Functionality\FilterFunctionality;
use App\CoreModule\Model\Persistent\Functionality\GroupFunctionality;
use App\CoreModule\Model\Persistent\Functionality\LogoFunctionality;
use App\CoreModule\Model\Persistent\Functionality\ProblemFinal\ArithmeticSequenceFinalFunctionality;
use App\CoreModule\Model\Persistent\Functionality\ProblemFinal\GeometricSequenceFinalFunctionality;
use App\CoreModule\Model\Persistent\Functionality\ProblemFinal\LinearEquationFinalFunctionality;
use App\CoreModule\Model\Persistent\Functionality\ProblemFinal\ProblemFinalFunctionality;
use App\CoreModule\Model\Persistent\Functionality\ProblemFinal\QuadraticEquationFinalFunctionality;
use App\CoreModule\Model\Persistent\Functionality\ProblemFinalTestVariantAssociationFunctionality;
use App\CoreModule\Model\Persistent\Functionality\ProblemFunctionality;
use App\CoreModule\Model\Persistent\Functionality\ProblemTemplate\ArithmeticSequenceTemplateFunctionality;
use App\CoreModule\Model\Persistent\Functionality\ProblemTemplate\GeometricSequenceTemplateFunctionality;
use App\CoreModule\Model\Persistent\Functionality\ProblemTemplate\LinearEquationTemplateFunctionality;
use App\CoreModule\Model\Persistent\Functionality\ProblemTemplate\QuadraticEquationTemplateFunctionality;
use App\CoreModule\Model\Persistent\Functionality\ProblemTypeFunctionality;
use App\CoreModule\Model\Persistent\Functionality\SubThemeFunctionality;
use App\CoreModule\Model\Persistent\Functionality\SuperGroupFunctionality;
use App\CoreModule\Model\Persistent\Functionality\TemplateJsonDataFunctionality;
use App\CoreModule\Model\Persistent\Functionality\TestFunctionality;
use App\CoreModule\Model\Persistent\Functionality\TestVariantFunctionality;
use App\CoreModule\Model\Persistent\Functionality\UserFunctionality;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Manager\HomepageStatisticsManager;
use App\CoreModule\Model\Persistent\Repository\ThemeRepository;
use App\CoreModule\Model\Persistent\Repository\DifficultyRepository;
use App\CoreModule\Model\Persistent\Repository\FilterRepository;
use App\CoreModule\Model\Persistent\Repository\GroupRepository;
use App\CoreModule\Model\Persistent\Repository\LogoRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemConditionRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemConditionTypeRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemFinal\ArithmeticSequenceFinalRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemFinal\GeometricSequenceFinalRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemFinal\LinearEquationFinalRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemFinal\ProblemFinalRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemFinal\QuadraticEquationFinalRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemFinalTestVariantAssociationRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemTemplate\ArithmeticSequenceTemplateRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemTemplate\GeometricSequenceTemplateRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemTemplate\LinearEquationTemplateRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemTemplate\ProblemTemplateRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemTemplate\QuadraticEquationTemplateRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemTypeRepository;
use App\CoreModule\Model\Persistent\Repository\RoleRepository;
use App\CoreModule\Model\Persistent\Repository\SubThemeRepository;
use App\CoreModule\Model\Persistent\Repository\SuperGroupRepository;
use App\CoreModule\Model\Persistent\Repository\TemplateJsonDataRepository;
use App\CoreModule\Model\Persistent\Repository\TestRepository;
use App\CoreModule\Model\Persistent\Repository\TestVariantRepository;
use App\CoreModule\Model\Persistent\Repository\UserRepository;
use App\CoreModule\Model\Persistent\Repository\ValidationFunctionRepository;
use App\CoreModule\Services\Authenticator;
use App\CoreModule\Services\Authorizator;
use App\CoreModule\Services\FileService;
use App\CoreModule\Services\MailService;
use App\CoreModule\Services\PasswordGenerator;
use App\CoreModule\Services\Validator;
use App\TeacherModule\Services\ParameterParser;
use Nette\DI\ContainerBuilder;

/**
 * Class CoreModuleExtension
 * @package App\CoreModule\DI
 */
class CoreModuleExtension extends ModuleExtension
{
    /**
     * @var array
     */
    protected $defaults = [
        'coreTemplatesDir' => CORE_MODULE_TEMPLATES_DIR,
        'studentTemplatesDir' => STUDENT_MODULE_TEMPLATES_DIR,
        'teacherTemplatesDir' => TEACHER_MODULE_TEMPLATES_DIR,
        'logosDir' => LOGOS_DIR,
        'logosTmpDir' => LOGOS_TMP_DIR,
        'loginURL' => null
    ];

    /**
     * @var string
     */
    protected $configFile = CORE_MODULE_DIR . 'DI' . DIRECTORY_SEPARATOR . 'config.neon';

    /**
     * @var array
     */
    protected $configRequiredItems = [ 'loginURL' ];

    /**
     * @param ContainerBuilder $builder
     */
    public function addDefinitions(ContainerBuilder $builder): void
    {
        // Services definitions

        $builder->addDefinition($this->prefix('authenticator'))
            ->setType(Authenticator::class);

        $builder->addDefinition($this->prefix('authorizator'))
            ->setType(Authorizator::class);

        $builder->addDefinition($this->prefix('fileService'))
            ->setType(FileService::class)
            ->setArguments([
                'logosDir' => $this->config['logosDir'],
                'logosTmpDir' => $this->config['logosTmpDir'],
                'coreTemplatesDir' => $this->config['coreTemplatesDir'],
                'studentTemplatesDir' => $this->config['studentTemplatesDir'],
                'teacherTemplatesDir' => $this->config['teacherTemplatesDir']
            ]);

        $builder->addDefinition($this->prefix('mailService'))
            ->setType(MailService::class)
            ->setArguments([
                'coreTemplatesDir' => $this->config['coreTemplatesDir'],
                'loginURL' => $this->config['loginURL']
            ]);

        $builder->addDefinition($this->prefix('validator'))
            ->setType(Validator::class);

        $builder->addDefinition($this->prefix('passwordGenerator'))
            ->setType(PasswordGenerator::class);

        $builder->addDefinition($this->prefix('parameterParser'))
            ->setType(ParameterParser::class);


        // Helpers definitions

        $builder->addDefinition($this->prefix('constHelper'))
            ->setType(ConstHelper::class);

        $builder->addDefinition($this->prefix('flashesTranslator'))
            ->setType(FlashesTranslator::class);

        $builder->addDefinition($this->prefix('formatterHelper'))
            ->setType(FormatterHelper::class);

        $builder->addDefinition($this->prefix('regularExpressions'))
            ->setType(RegularExpressions::class);

        $builder->addDefinition($this->prefix('stringsHelper'))
            ->setType(StringsHelper::class);


        // Factories definitions

        $builder->addDefinition($this->prefix('helpModalFactory'))
            ->setImplement(IHelpModalFactory::class);

        $builder->addDefinition($this->prefix('headerBarFactory'))
            ->setImplement(IHeaderBarFactory::class);

        $builder->addDefinition($this->prefix('sideBarFactory'))
            ->setImplement(ISideBarFactory::class);

        $builder->addDefinition($this->prefix('passwordFormFactory'))
            ->setImplement(IPasswordFormFactory::class);

        $builder->addDefinition($this->prefix('signFormFactory'))
            ->setImplement(ISignFormFactory::class);

        $builder->addDefinition($this->prefix('forgetPasswordFactory'))
            ->setImplement(IForgetPasswordFactory::class);

        $builder->addDefinition($this->prefix('forgetPasswordFormFactory'))
            ->setImplement(IForgetPasswordFormFactory::class);


        // Repositories definitions

        $builder->addDefinition($this->prefix('userRepository'))
            ->setType(UserRepository::class)
            ->setTags([ 'doctrine.repositoryEntity' => User::class ]);

        $builder->addDefinition($this->prefix('roleRepository'))
            ->setType(RoleRepository::class)
            ->setTags([ 'doctrine.repositoryEntity' => Role::class ]);

        $builder->addDefinition($this->prefix('problemRepository'))
            ->setType(ProblemRepository::class)
            ->setTags([ 'doctrine.repositoryEntity' => Problem::class ]);

        $builder->addDefinition($this->prefix('problemFinalRepository'))
            ->setType(ProblemFinalRepository::class)
            ->setTags([ 'doctrine.repositoryEntity' => ProblemFinal::class ]);

        $builder->addDefinition($this->prefix('linearEquationFinalRepository'))
            ->setType(LinearEquationFinalRepository::class)
            ->setTags([ 'doctrine.repositoryEntity' => LinearEquationFinal::class ]);

        $builder->addDefinition($this->prefix('quadraticEquationFinalRepository'))
            ->setType(QuadraticEquationFinalRepository::class)
            ->setTags([ 'doctrine.repositoryEntity' => QuadraticEquationFinal::class ]);

        $builder->addDefinition($this->prefix('arithmeticSequenceFinalRepository'))
            ->setType(ArithmeticSequenceFinalRepository::class)
            ->setTags([ 'doctrine.repositoryEntity' => ArithmeticSequenceFinal::class ]);

        $builder->addDefinition($this->prefix('geometricSequenceFinalRepository'))
            ->setType(GeometricSequenceFinalRepository::class)
            ->setTags([ 'doctrine.repositoryEntity' => GeometricSequenceFinal::class ]);

        $builder->addDefinition($this->prefix('problemTemplateRepository'))
            ->setType(ProblemTemplateRepository::class)
            ->setTags([ 'doctrine.repositoryEntity' => ProblemTemplate::class ]);

        $builder->addDefinition($this->prefix('problemTypeRepository'))
            ->setType(ProblemTypeRepository::class)
            ->setTags([ 'doctrine.repositoryEntity' => ProblemType::class ]);

        $builder->addDefinition($this->prefix('difficultyRepository'))
            ->setType(DifficultyRepository::class)
            ->setTags([ 'doctrine.repositoryEntity' => Difficulty::class ]);

        $builder->addDefinition($this->prefix('subThemeRepository'))
            ->setType(SubThemeRepository::class)
            ->setTags([ 'doctrine.repositoryEntity' => SubTheme::class ]);

        $builder->addDefinition($this->prefix('themeRepository'))
            ->setType(ThemeRepository::class)
            ->setTags([ 'doctrine.repositoryEntity' => Theme::class ]);

        $builder->addDefinition($this->prefix('problemConditionTypeRepository'))
            ->setType(ProblemConditionTypeRepository::class)
            ->setTags([ 'doctrine.repositoryEntity' => ProblemConditionType::class ]);

        $builder->addDefinition($this->prefix('problemConditionRepository'))
            ->setType(ProblemConditionRepository::class)
            ->setTags([ 'doctrine.repositoryEntity' => ProblemCondition::class ]);

        $builder->addDefinition($this->prefix('linearEquationTemplateRepository'))
            ->setType(LinearEquationTemplateRepository::class)
            ->setTags([ 'doctrine.repositoryEntity' => LinearEquationTemplate::class ]);

        $builder->addDefinition($this->prefix('quadraticEquationTemplateRepository'))
            ->setType(QuadraticEquationTemplateRepository::class)
            ->setTags([ 'doctrine.repositoryEntity' => QuadraticEquationTemplate::class ]);

        $builder->addDefinition($this->prefix('arithmeticSequenceTemplateRepository'))
            ->setType(ArithmeticSequenceTemplateRepository::class)
            ->setTags([ 'doctrine.repositoryEntity' => ArithmeticSequenceTemplate::class ]);

        $builder->addDefinition($this->prefix('geometricSequenceTemplateRepository'))
            ->setType(GeometricSequenceTemplateRepository::class)
            ->setTags([ 'doctrine.repositoryEntity' => GeometricSequenceTemplate::class ]);

        $builder->addDefinition($this->prefix('superGroupRepository'))
            ->setType(SuperGroupRepository::class)
            ->setTags([ 'doctrine.repositoryEntity' => SuperGroup::class ]);

        $builder->addDefinition($this->prefix('groupRepository'))
            ->setType(GroupRepository::class)
            ->setTags([ 'doctrine.repositoryEntity' => Group::class ]);

        $builder->addDefinition($this->prefix('logoRepository'))
            ->setType(LogoRepository::class)
            ->setTags([ 'doctrine.repositoryEntity' => Logo::class ]);

        $builder->addDefinition($this->prefix('testRepository'))
            ->setType(TestRepository::class)
            ->setTags([ 'doctrine.repositoryEntity' => Test::class ]);

        $builder->addDefinition($this->prefix('testVariantRepository'))
            ->setType(TestVariantRepository::class)
            ->setTags([ 'doctrine.repositoryEntity' => TestVariant::class ]);

        $builder->addDefinition($this->prefix('problemFinalTestVariantAssociationRepository'))
            ->setType(ProblemFinalTestVariantAssociationRepository::class)
            ->setTags([ 'doctrine.repositoryEntity' => ProblemFinalTestVariantAssociation::class ]);

        $builder->addDefinition($this->prefix('templateJsonDataRepository'))
            ->setType(TemplateJsonDataRepository::class)
            ->setTags([ 'doctrine.repositoryEntity' => TemplateJsonData::class ]);

        $builder->addDefinition($this->prefix('validationFunctionRepository'))
            ->setType(ValidationFunctionRepository::class)
            ->setTags([ 'doctrine.repositoryEntity' => ValidationFunction::class ]);

        $builder->addDefinition($this->prefix('filterRepository'))
            ->setType(FilterRepository::class)
            ->setTags([ 'doctrine.repositoryEntity' => Filter::class ]);


        // Functionalities definitions

        $builder->addDefinition($this->prefix('userFunctionality'))
            ->setType(UserFunctionality::class);

        $builder->addDefinition($this->prefix('problemFinalFunctionality'))
            ->setType(ProblemFinalFunctionality::class);

        $builder->addDefinition($this->prefix('linearEquationFinalFunctionality'))
            ->setType(LinearEquationFinalFunctionality::class);

        $builder->addDefinition($this->prefix('quadraticEquationFinalFunctionality'))
            ->setType(QuadraticEquationFinalFunctionality::class);

        $builder->addDefinition($this->prefix('arithmeticSequenceFinalFunctionality'))
            ->setType(ArithmeticSequenceFinalFunctionality::class);

        $builder->addDefinition($this->prefix('geometricSequenceFinalFunctionality'))
            ->setType(GeometricSequenceFinalFunctionality::class);

        $builder->addDefinition($this->prefix('themeFunctionality'))
            ->setType(ThemeFunctionality::class);

        $builder->addDefinition($this->prefix('subThemeFunctionality'))
            ->setType(SubThemeFunctionality::class);

        $builder->addDefinition($this->prefix('problemFunctionality'))
            ->setType(ProblemFunctionality::class);

        $builder->addDefinition($this->prefix('problemTypeFunctionality'))
            ->setType(ProblemTypeFunctionality::class);

        $builder->addDefinition($this->prefix('problemFinalTestVariantAssociationFunctionality'))
            ->setType(ProblemFinalTestVariantAssociationFunctionality::class);

        $builder->addDefinition($this->prefix('linearEquationTemplateFunctionality'))
            ->setType(LinearEquationTemplateFunctionality::class);

        $builder->addDefinition($this->prefix('quadraticEquationTemplateFunctionality'))
            ->setType(QuadraticEquationTemplateFunctionality::class);

        $builder->addDefinition($this->prefix('arithmeticSequenceTemplateFunctionality'))
            ->setType(ArithmeticSequenceTemplateFunctionality::class);

        $builder->addDefinition($this->prefix('geometricSequenceTemplateFunctionality'))
            ->setType(GeometricSequenceTemplateFunctionality::class);

        $builder->addDefinition($this->prefix('superGroupFunctionality'))
            ->setType(SuperGroupFunctionality::class);

        $builder->addDefinition($this->prefix('groupFunctionality'))
            ->setType(GroupFunctionality::class);

        $builder->addDefinition($this->prefix('logoFunctionality'))
            ->setType(LogoFunctionality::class);

        $builder->addDefinition($this->prefix('testFunctionality'))
            ->setType(TestFunctionality::class);

        $builder->addDefinition($this->prefix('testVariantFunctionality'))
            ->setType(TestVariantFunctionality::class);

        $builder->addDefinition($this->prefix('templateJsonDataFunctionality'))
            ->setType(TemplateJsonDataFunctionality::class);

        $builder->addDefinition($this->prefix('filterFunctionality'))
            ->setType(FilterFunctionality::class);


        // Managers definitions

        $builder->addDefinition($this->prefix('constraintEntityManager'))
            ->setType(ConstraintEntityManager::class);

        $builder->addDefinition($this->prefix('homepageStatisticsManager'))
            ->setType(HomepageStatisticsManager::class);
    }
}