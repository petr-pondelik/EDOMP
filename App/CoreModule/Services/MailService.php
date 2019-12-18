<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.9.19
 * Time: 20:14
 */

namespace App\CoreModule\Services;

use App\CoreModule\Helpers\ConstHelper;
use App\CoreModule\Model\Persistent\Entity\User;
use Nette\Application\UI\ITemplate;
use Nette\Application\UI\ITemplateFactory;
use Nette\Mail\IMailer;
use Nette\Mail\Message;
use Nette\Mail\SendException;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class MailService
 * @package App\CoreModule\Services
 */
final class MailService
{
    /**
     * @var IMailer
     */
    protected $mailer;

    /**
     * @var ITemplateFactory
     */
    protected $templateFactory;

    /**
     * @var ConstHelper
     */
    protected $constHelper;

    /**
     * @var string
     */
    protected $coreTemplatesDir;

    /**
     * @var string
     */
    private $studentLoginUrl;

    /**
     * @var string
     */
    private $teacherLoginUrl;

    /**
     * @var string
     */
    protected $from = 'EDOMP <edomp@wiedzmin.4fan.cz>';

    /**
     * @var string
     */
    protected $subjectPrefix = 'EDOMP | ';

    /**
     * MailService constructor.
     * @param IMailer $mailer
     * @param ITemplateFactory $templateFactory
     * @param ConstHelper $constHelper
     * @param string $coreTemplatesDir
     * @param string $studentLoginUrl
     * @param string $teacherLoginUrl
     */
    public function __construct
    (
        IMailer $mailer,
        ITemplateFactory $templateFactory,
        ConstHelper $constHelper,
        string $coreTemplatesDir,
        string $studentLoginUrl,
        string $teacherLoginUrl
    )
    {
        $this->mailer = $mailer;
        $this->templateFactory = $templateFactory;
        $this->constHelper = $constHelper;
        $this->coreTemplatesDir = $coreTemplatesDir;
        $this->studentLoginUrl = $studentLoginUrl;
        $this->teacherLoginUrl = $teacherLoginUrl;
    }

    /**
     * @return \Nette\Application\UI\ITemplate
     */
    public function createTemplate(): ITemplate
    {
        return $this->templateFactory->createTemplate();
    }

    /**
     * @param Message $message
     * @param string $email
     */
    public function sendEmailTo(Message $message, string $email): void
    {
        try{
            $message->addTo($email);
            $this->mailer->send($message);
        } catch (SendException $e){
            Debugger::log($e, ILogger::EXCEPTION);
        }
    }

    /**
     * @param User $user
     * @param string $password
     */
    public function sendInvitationEmail(User $user, string $password): void
    {
        $template = $this->createTemplate();
        $template->setFile($this->coreTemplatesDir . DIRECTORY_SEPARATOR . 'mail' . DIRECTORY_SEPARATOR . 'invitation.latte');
        $template->user = $user;
        $template->password = $password;
        $template->loginURL = in_array($user->getRole()->getId(), $this->constHelper::ADMIN_TEACHER_ROLES, true) ? $this->teacherLoginUrl : $this->studentLoginUrl;
        $message = new Message();
        $message->setFrom($this->from)
            ->setSubject($this->subjectPrefix . 'Pozvání do aplikace')
            ->setHtmlBody($template);
        $this->sendEmailTo($message, $user->getEmail());
    }

    /**
     * @param User $user
     * @param string $password
     */
    public function sendPasswordResetEmail(User $user, string $password): void
    {
        $template = $this->createTemplate();
        $template->setFile($this->coreTemplatesDir . DIRECTORY_SEPARATOR . 'mail' . DIRECTORY_SEPARATOR . 'passwordReset.latte');
        $template->user = $user;
        $template->password = $password;
        $template->loginURL = in_array($user->getRole()->getId(), $this->constHelper::ADMIN_TEACHER_ROLES, true) ? $this->teacherLoginUrl : $this->studentLoginUrl;
        $message = new Message();
        $message->setFrom($this->from)
            ->setSubject($this->subjectPrefix . 'Reset hesla')
            ->setHtmlBody($template);
        $this->sendEmailTo($message, $user->getEmail());
    }
}