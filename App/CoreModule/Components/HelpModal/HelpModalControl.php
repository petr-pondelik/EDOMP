<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 16.6.19
 * Time: 23:20
 */

namespace App\CoreModule\Components\HelpModal;

use App\CoreModule\Components\EDOMPControl;
use Nette\Application\UI\Presenter;
use Nette\Utils\Strings;

/**
 * Class HelpModalControl
 * @package App\CoreModule\Components\HelpModal
 */
final class HelpModalControl extends EDOMPControl
{
    /**
     * @return string
     */
    public function getHelpContentName(): string
    {
        $presenterName = $this->presenter->getName();
        if (Strings::contains($presenterName, 'Template')) {
            return 'ProblemTemplate';
        }
        if (Strings::startsWith($presenterName, 'Teacher:Settings')) {
            return 'Settings' . Strings::firstUpper($this->presenter->getAction());
        }
        return Strings::after($presenterName, ':');
    }

    public function render(): void
    {
        $this->template->type = $this->getHelpContentName();
        parent::render();
    }
}