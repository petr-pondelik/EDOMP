<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 16.6.19
 * Time: 23:20
 */

namespace App\CoreModule\Components\HelpModal;

use App\CoreModule\Components\EDOMPControl;
use Nette\Utils\Strings;

/**
 * Class HelpModalControl
 * @package App\CoreModule\Components\HelpModal
 */
final class HelpModalControl extends EDOMPControl
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
        parent::render();
    }
}