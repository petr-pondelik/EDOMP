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
     * MailService constructor.
     * @param IMailer $mailer
     * @param ITemplateFactory $templateFactory
     * @param string $templateDir
     */
    public function __construct
    (
        IMailer $mailer,
        ITemplateFactory $templateFactory,
        string $templateDir
    )
    {
        $this->mailer = $mailer;
        $this->templateFactory = $templateFactory;
        $this->templateDir = $templateDir;
    }

    /**
     * @param User $user
     * @return \Nette\Application\UI\ITemplate
     */
    public function createTemplate(User $user): ITemplate
    {
        $template = $this->templateFactory->createTemplate();
        $template->setFile($this->templateDir . '/mail/invitation.latte');
        $template->user = $user;
        return $template;
    }

    /**
     * @param User $user
     */
    public function sendInvitationEmail(User $user): void
    {
        $template = $this->createTemplate($user);
        $message = new Message();
        $message->setFrom('EDOMP <edomp@wiedzmin.4fan.cz>')
            ->setSubject('Pozvání do aplikace')
            ->setHtmlBody($template);
        // TODO: REPLACE USERNAME WITH EMAIL !!!
        $this->sendEmailTo($message, $user->getUsername());
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