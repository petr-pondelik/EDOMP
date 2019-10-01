<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.9.19
 * Time: 20:14
 */

namespace App\Services;

use App\Model\Persistent\Entity\User;
use Nette\Application\UI\ITemplate;
use Nette\Application\UI\ITemplateFactory;
use Nette\Mail\IMailer;
use Nette\Mail\Message;
use Nette\Mail\SendException;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * Class MailService
 * @package App\Services
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
    protected $templateDir;

    /**
     * @var string
     */
    protected $loginURL;

    /**
     * MailService constructor.
     * @param IMailer $mailer
     * @param ITemplateFactory $templateFactory
     * @param string $templateDir
     * @param string $loginURL
     */
    public function __construct
    (
        IMailer $mailer,
        ITemplateFactory $templateFactory,
        string $templateDir,
        string $loginURL
    )
    {
        $this->mailer = $mailer;
        $this->templateFactory = $templateFactory;
        $this->templateDir = $templateDir;
        $this->loginURL = $loginURL;
    }

    /**
     * @return \Nette\Application\UI\ITemplate
     */
    public function createTemplate(): ITemplate
    {
        return $this->templateFactory->createTemplate();
    }

    /**
     * @param User $user
     * @param string $password
     */
    public function sendInvitationEmail(User $user, string $password): void
    {
        $template = $this->createTemplate();
        $template->setFile($this->templateDir . '/mail/invitation.latte');
        $template->user = $user;
        $template->password = $password;
        $template->loginURL = $this->loginURL;
        $message = new Message();
        $message->setFrom('EDOMP <edomp@wiedzmin.4fan.cz>')
            ->setSubject('Pozvání do aplikace')
            ->setHtmlBody($template);
        $this->sendEmailTo($message, $user->getEmail());
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
}