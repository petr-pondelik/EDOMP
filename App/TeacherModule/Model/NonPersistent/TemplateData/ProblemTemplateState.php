<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.9.19
 * Time: 20:51
 */

namespace App\TeacherModule\Model\NonPersistent\TemplateData;

use Nette\Utils\ArrayHash;
use Nette\Utils\Strings;

/**
 * Class ProblemTemplateState
 * @package App\TeacherModule\Model\NonPersistent\TemplateData
 */
final class ProblemTemplateState
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

    public function invalidate(): void
    {
        foreach ($this->problemTemplateStateItems as $problemTemplateStateItem) {
            $problemTemplateStateItem->setValidated(false);
        }
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
        foreach ($problemTemplateStatusItemArr as $problemTemplateStateItem) {
            $this->problemTemplateStateItems[$problemTemplateStateItem->getRule()] = $problemTemplateStateItem;
        }
    }

    /**
     * @return bool
     */
    public function isTypeValidated(): bool
    {
        if (!isset($this->problemTemplateStateItems['type'])) {
            return false;
        }
        return $this->problemTemplateStateItems['type']->isValidated();
    }

    /**
     * @param ArrayHash $values
     * @return array
     */
    public function conditionsValidated(ArrayHash $values): array
    {
        bdump('CONDITIONS VALIDATED');
        $stateItems = $this->getProblemTemplateStateItems();
        foreach ($values as $key => $value) {
            if (
                $value !== 0 &&
                Strings::match($key, '~condition_\d~') &&
                ($value !== (int)$stateItems[$key]->getValue() ||
                    !$stateItems[$key]->isValidated())
            ) {
                return [
                    'validated' => false,
                    'toValidate' => Strings::match($key, '~condition_(\d)~')[1]
                ];
            }
        }
        return [
            'validated' => true,
            'toValidate' => null
        ];
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