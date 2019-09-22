<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 16.6.19
 * Time: 23:20
 */

namespace App\Components\SectionHelpModal;

use App\Components\EDOMPControl;
use Nette\Utils\Strings;

/**
 * Class ProblemTemplateHelpControl
 * @package App\Components\ProblemTemplateHelp
 */
class SectionHelpModalControl extends EDOMPControl
{
    /**
     * @param string $presenterName
     * @return string
     */
    public function getHelpContentName(string $presenterName): string
    {
        if(Strings::contains($presenterName, 'Template')){
            return 'ProblemTemplate';
        }
        return Strings::after($presenterName, ':');
    }

    public function render(): void
    {
        $this->template->type = $this->getHelpContentName($this->presenter->name);
        $this->template->render(__DIR__ . '/templates/problemTemplateHelp.latte');
    }
}