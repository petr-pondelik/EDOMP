<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.9.19
 * Time: 20:14
 */

namespace App\CoreModule\Services;

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
class MailService
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
     * @var string
     */
    protected $coreTemplatesDir;

    /**
     * @var string
     */
    protected $loginURL;

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
     * @param string $coreTemplatesDir
     * @param string $loginURL
     */
    public function __construct
    (
        IMailer $mailer,
        ITemplateFactory $templateFactory,
        string $coreTemplatesDir,
        string $loginURL
    )
    {
        $this->mailer = $mailer;
        $this->templateFactory = $templateFactory;
        $this->coreTemplatesDir = $coreTemplatesDir;
        $this->loginURL = $loginURL;
        bdump($this->mailer);
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
        $template->setFile($this->coreTemplatesDir . 'mail' . DIRECTORY_SEPARATOR . 'invitation.latte');
        $template->user = $user;
        $template->password = $password;
        $template->loginURL = $this->loginURL;
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
        $template->setFile($this->coreTemplatesDir . 'mail' . DIRECTORY_SEPARATOR . 'passwordReset.latte');
        $template->user = $user;
        $template->password = $password;
        $template->loginURL = $this->loginURL;
        $message = new Message();
        $message->setFrom($this->from)
            ->setSubject($this->subjectPrefix . 'Reset hesla')
            ->setHtmlBody($template);
        $this->sendEmailTo($message, $user->getEmail());
    }
}