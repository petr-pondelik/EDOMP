<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 16.6.19
 * Time: 23:20
 */

namespace App\Components\ProblemTemplateHelp;

use Nette\Application\UI\Control;

/**
 * Class ProblemTemplateHelpControl
 * @package App\Components\ProblemTemplateHelp
 */
class ProblemTemplateHelpControl extends Control
{
    public function render(): void
    {
        $this->template->render(__DIR__ . '/templates/problemTemplateHelp.latte');
    }
}