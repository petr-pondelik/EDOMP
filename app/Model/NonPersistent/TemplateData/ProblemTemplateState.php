<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.9.19
 * Time: 20:51
 */

namespace App\Model\NonPersistent\TemplateData;

use App\Model\Persistent\Entity\ProblemConditionType;
use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;

/**
 * Class ProblemTemplateState
 * @package App\Services
 */
class ProblemTemplateState
{
    /**
     * @var ProblemTemplateStateItem[]
     */
    protected $problemTemplateStateItems;

    /**
     * ProblemTemplateState constructor.
     */
    public function __construct()
    {
        $this->problemTemplateStateItems = [];
    }

    public function reset(): void
    {
        $this->problemTemplateStateItems = [];
    }

    /**
     * @param ProblemTemplateStateItem $problemTemplateStatusItem
     */
    public function update(ProblemTemplateStateItem $problemTemplateStatusItem): void
    {
        bdump('UPDATE PROBLEM TEMPLATE STATE');
        $this->problemTemplateStateItems[$problemTemplateStatusItem->getRule()] = $problemTemplateStatusItem;
    }

    /**
     * @param ProblemTemplateStateItem[] $problemTemplateStatusItemArr
     */
    public function updateArr(array $problemTemplateStatusItemArr): void
    {
        foreach ($problemTemplateStatusItemArr as $problemTemplateStateItem){
            $this->problemTemplateStateItems[$problemTemplateStateItem->getRule()] = $problemTemplateStateItem;
        }
    }

    /**
     * @return bool
     */
    public function isTypeValidated(): bool
    {
        if(!isset($this->problemTemplateStateItems['type'])){
            return false;
        }
        return (bool) $this->problemTemplateStateItems['type']->getValue();
    }

    /**
     * @param ArrayHash $values
     * @return bool
     */
    public function conditionsValidated(ArrayHash $values): bool
    {
        bdump('CONDITIONS VALIDATED');
        $stateItems = $this->getProblemTemplateStateItems();
        bdump($stateItems);
        bdump($values);
        foreach ($values as $key => $value){
            if(Strings::match($key, '~condition_\d~')){
                bdump($value);
                bdump($stateItems[$key]->getValue());
            }
            if(
                $value !== 0 &&
                Strings::match($key, '~condition_\d~') &&
                ($value !== (int) $stateItems[$key]->getValue() ||
                !$stateItems[$key]->isValidated())
            ){
                bdump($value);
                bdump($stateItems[$key]);
                return false;
            }
        }
        return true;
    }

    /**
     * @return ProblemTemplateStateItem[]
     */
    public function getProblemTemplateStateItems(): array
    {
        return $this->problemTemplateStateItems;
    }

    /**
     * @param ProblemTemplateStateItem[] $problemTemplateStateItems
     */
    public function setProblemTemplateStateItems(array $problemTemplateStateItems): void
    {
        $this->problemTemplateStateItems = $problemTemplateStateItems;
    }
}