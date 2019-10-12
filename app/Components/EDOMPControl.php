<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.9.19
 * Time: 17:11
 */

namespace App\Components;


use Nette\Application\UI\Control;
use ReflectionClass;

/**
 * Class BaseControl
 * @package App\Components
 */
abstract class EDOMPControl extends Control
{
    /**
     * @return string
     * @throws \ReflectionException
     */
    public function getDir(): string
    {
        return dirname((new ReflectionClass(static::class))->getFileName());
    }

    /**
     * @return string
     */
    public function getTemplateName(): string
    {
        return 'default';
    }

    /**
     * @throws \ReflectionException
     */
    public function render(): void
    {
        $this->template->render($this->getDir() . '/templates/' . $this->getTemplateName() . '.latte');
    }

    public function initComponents(): void {}

    public function fillComponents(): void {}
}