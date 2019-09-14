<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.3.19
 * Time: 23:18
 */

namespace App\Services;

use App\Helpers\ConstHelper;
use App\Helpers\LatexHelper;
use App\Helpers\StringsHelper;
use App\Model\Persistent\Repository\ProblemFinal\ProblemFinalRepository;
use App\Plugins\ArithmeticSequencePlugin;
use App\Plugins\GeometricSequencePlugin;
use App\Plugins\LinearEquationPlugin;
use App\Plugins\ProblemPlugin;
use App\Plugins\QuadraticEquationPlugin;
use jlawrence\eos\Parser;
use Nette\Utils\Strings;

/**
 * Class PluginContainer
 * @package App\Services
 */
class PluginContainer
{
    /**
     * @var NewtonApiClient
     */
    protected $newtonApiClient;

    /**
     * @var ProblemFinalRepository
     */
    protected $problemFinalRepository;

    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * @var StringsHelper
     */
    protected $stringsHelper;

    /**
     * @var LatexHelper
     */
    protected $latexHelper;

    /**
     * @var LinearEquationPlugin
     */
    protected $linearEquationPlugin;

    /**
     * @var QuadraticEquationPlugin
     */
    protected $quadraticEquationPlugin;

    /**
     * @var ArithmeticSequencePlugin
     */
    protected $arithmeticSequencePlugin;

    /**
     * @var GeometricSequencePlugin
     */
    protected $geometricSequencePlugin;

    /**
     * @var array
     */
    public $plugins = [];

    /**
     * @var array
     */
    public $evaluate = [];

    /**
     * PluginContainer constructor.
     * @param NewtonApiClient $newtonApiClient
     * @param ProblemFinalRepository $problemFinalRepository
     * @param ConstHelper $constHelper
     * @param StringsHelper $stringsHelper
     * @param LatexHelper $latexHelper
     * @param Parser $parser
     * @param LinearEquationPlugin $linearEquationPlugin
     * @param QuadraticEquationPlugin $quadraticEquationPlugin
     * @param ArithmeticSequencePlugin $arithmeticSequencePlugin
     * @param GeometricSequencePlugin $geometricSequencePlugin
     */
    public function __construct
    (
        NewtonApiClient $newtonApiClient,
        ProblemFinalRepository $problemFinalRepository,
        ConstHelper $constHelper, StringsHelper $stringsHelper, LatexHelper $latexHelper,
        Parser $parser,
        LinearEquationPlugin $linearEquationPlugin,
        QuadraticEquationPlugin $quadraticEquationPlugin,
        ArithmeticSequencePlugin $arithmeticSequencePlugin,
        GeometricSequencePlugin $geometricSequencePlugin
    )
    {
        $this->newtonApiClient = $newtonApiClient;
        $this->problemFinalRepository = $problemFinalRepository;
        $this->parser = $parser;
        $this->constHelper = $constHelper;
        $this->stringsHelper = $stringsHelper;
        $this->latexHelper = $latexHelper;
        $this->linearEquationPlugin = $linearEquationPlugin;
        $this->quadraticEquationPlugin = $quadraticEquationPlugin;
        $this->arithmeticSequencePlugin = $arithmeticSequencePlugin;
        $this->geometricSequencePlugin = $geometricSequencePlugin;
    }

    /**
     * @param string $problemTypeKeyLabel
     * @return ProblemPlugin
     */
    public function getPlugin(string $problemTypeKeyLabel): ProblemPlugin
    {
        return $this->{Strings::firstLower($problemTypeKeyLabel) . 'Plugin'};
    }
}