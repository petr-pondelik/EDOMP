<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 21.9.19
 * Time: 17:11
 */

namespace App\CoreModule\Components;


use Nette\Application\UI\Control;
use ReflectionClass;

/**
 * Class EDOMPControl
 * @package App\CoreModule\Components
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

    /**
     * @param iterable|null $args
     */
    public function initComponents(iterable $args = null): void {}

    /**
     * @param iterable|null $args
     */
    public function fillComponents(iterable $args = null): void {}
}