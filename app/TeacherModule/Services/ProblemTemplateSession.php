<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 6.9.19
 * Time: 20:39
 */

namespace App\TeacherModule\Services;

use App\TeacherModule\Model\NonPersistent\Entity\ProblemTemplateNP;
use App\TeacherModule\Model\NonPersistent\TemplateData\ProblemTemplateState;
use App\TeacherModule\Model\NonPersistent\TemplateData\ProblemTemplateStateItem;
use App\CoreModule\Interfaces\IEDOMPSession;
use Nette\Http\Session;
use Nette\Http\SessionSection;

/**
 * Class ProblemTemplateSession
 * @package App\TeacherModule\Services
 */
class ProblemTemplateSession implements IEDOMPSession
{
    /**
     * @var SessionSection
     */
    protected $problemTemplateSession;

    /**
     * ProblemTemplateSession constructor.
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        $this->problemTemplateSession = $session->getSection('problemTemplateSession');
    }

    public function erase(): void
    {
        $this->problemTemplateSession->problemTemplate = null;
        $this->problemTemplateSession->defaultState = null;
    }

    /**
     * @return ProblemTemplateNP|null
     */
    public function getProblemTemplate(): ?ProblemTemplateNP
    {
        return $this->problemTemplateSession->problemTemplate;
    }

    /**
     * @param ProblemTemplateNP|null $template
     */
    public function setProblemTemplate(?ProblemTemplateNP $template): void
    {
        $this->problemTemplateSession->problemTemplate = $template;
    }

    /**
     * @param ProblemTemplateStateItem $item
     */
    public function addDefaultStateItem(ProblemTemplateStateItem $item): void
    {
        if(!$this->problemTemplateSession->defaultState){
            $this->problemTemplateSession->defaultState = new ProblemTemplateState();
        }
        $this->problemTemplateSession->defaultState->update($item);
    }

    /**
     * @return ProblemTemplateState|null
     */
    public function getDefaultState(): ?ProblemTemplateState
    {
        return $this->problemTemplateSession->defaultState;
    }
}