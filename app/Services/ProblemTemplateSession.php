<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 6.9.19
 * Time: 20:39
 */

namespace App\Services;

use App\Model\NonPersistent\Entity\ProblemTemplateNP;
use Nette\Http\Session;
use Nette\Http\SessionSection;

/**
 * Class ProblemTemplateSession
 * @package App\Services
 */
class ProblemTemplateSession
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
    }

    /**
     * @return ProblemTemplateNP
     */
    public function getProblemTemplate(): ProblemTemplateNP
    {
        return $this->problemTemplateSession->problemTemplate;
    }

    /**
     * @param ProblemTemplateNP $template
     */
    public function setProblemTemplate(ProblemTemplateNP $template): void
    {
        $this->problemTemplateSession->problemTemplate = $template;
    }
}