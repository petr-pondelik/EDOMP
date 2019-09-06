<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.9.19
 * Time: 20:51
 */

namespace App\Services;

use App\Model\NonPersistent\Entity\ProblemTemplateStatusItem;
use App\Model\Persistent\Entity\ProblemConditionType;
use Interfaces\ISessionBase;
use Nette\Http\Session;
use Nette\Http\SessionSection;

/**
 * Class ProblemTemplateStatus
 * @package App\Services
 */
class ProblemTemplateStatus
{
    /**
     * @var SessionSection
     */
    protected $problemTemplateStatusSession;

    /**
     * ProblemTemplateStatus constructor.
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->problemTemplateStatusSession = $session->getSection('problemTemplateStatus');
    }

    public function getSerialized()
    {
        return $this->problemTemplateStatusSession->status;
    }

    /**
     * @return array
     */
    public function getUnserialized()
    {
        $res = [];
        foreach ($this->problemTemplateStatusSession->status as $key => $status){
            $res[$key] = unserialize($status, [ProblemTemplateStatusItem::class]);
        }
        return $res;
    }

    public function resetStatus()
    {
        $this->problemTemplateStatusSession->status = [];
    }

    public function updateStatus(ProblemTemplateStatusItem $problemTemplateStatusItem)
    {
        $status = $this->getSerialized();
        $status[$problemTemplateStatusItem->getRule()] = serialize($problemTemplateStatusItem);
        $this->problemTemplateStatusSession->status = $status;
    }

    /**
     * @return bool
     */
    public function isTypeValidated(): bool
    {
        if(!isset($this->problemTemplateStatusSession->status['type'])){
            return false;
        }
        $problemTypeStatus = unserialize($this->problemTemplateStatusSession->status['type'], [ProblemTemplateStatusItem::class]);
        return $problemTypeStatus->isValidated();
    }

    /**
     * @param ProblemConditionType[] $conditionTypes
     * @return bool
     */
    public function isAllValidated(array $conditionTypes): bool
    {
        if(!$this->isTypeValidated()){
            return false;
        }
        foreach ($conditionTypes as $conditionType){
            if(!isset($this->problemTemplateStatusSession->status[$conditionType->getId()])){
                return false;
            }
            $statusItem = unserialize($this->problemTemplateStatusSession->status[$conditionType->getId()], [ProblemTemplateStatusItem::class]);
            if(!$statusItem->isValidated()){
                return false;
            }
        }
        return true;
    }
}