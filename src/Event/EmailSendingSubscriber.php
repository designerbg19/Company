<?php

namespace App\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailSendingSubscriber implements EventSubscriberInterface
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @param MailerInterface $mailer
     */
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
       return [
         EmailEvent::COMPANY => 'onCreateCompany',
         EmailEvent::USER => 'onCreateUser',
         EmailEvent::ADMIBUSER => 'onSendToAdmin',
         EmailEvent::PUBLICITY => 'publicityPurchase',
         EmailEvent::NEWPUBLICITYSEARCH => 'newPublicitySearch',
         EmailEvent::ACCOUNTPRO => 'newAccountPro',
         EmailEvent::ACCOUNTPRODELETE => 'AccountProDelete',
       ];
    }

    /**
     * @param EmailEvent $event
     */
    public function onCreateCompany(EmailEvent $event)
    {
        $mail = $event->getTemplateMail();
        $user = $event->getUser();
        $template = $mail->getMessage();
        $template = preg_replace('#\$user.civility#', $user->getCivility(), $template);
        $template = preg_replace('#\$user.firstname#', $user->getFirstname(), $template);
        $template = preg_replace('#\$user.lastname#', $user->getLastname(), $template);
        $template = preg_replace('#\$company.name#', $event->getParam(), $template);
        $email = (new Email())
            ->from('alienmailcarrier@example.com')
            ->to('contact@rateacompany.com')
            ->subject($mail->getSubject())
            ->html($template);
        $this->mailer->send($email);
    }
    public function onCreateUser(EmailEvent $event)
    {
        $mail = $event->getTemplateMail();
        $user = $event->getUser();
        $template = $mail->getMessage();
        $template = preg_replace('#\$user.username#', $event->getParam(), $template);
        $email = (new Email())
            ->from('contact@rateacompany.com')
            ->to($user->getEmail())
            ->subject($mail->getSubject())
            ->html($template);
        $this->mailer->send($email);
    }
    public function onSendToAdmin(EmailEvent $event)
    {
        $mail = $event->getTemplateMail();
        $user = $event->getUser();
        $template = $mail->getMessage();
        $now = date_create()->format('Y-m-d H:i:s');
        $template = preg_replace('#\$user.subject#', $event->getParam()['subject'], $template);
        $template = preg_replace('#\$user.firstname#', $user->getFirstname(), $template);
        $template = preg_replace('#\$user.lastname#', $user->getLastname(), $template);
        $template = preg_replace('#\$user.email#', $user->getEmail(), $template);
        $template = preg_replace('#\$user.message#', $event->getParam()['message'], $template);
        $template = preg_replace('#\$user.createdDate#',$now, $template);
        $email = (new Email())
            ->from('contact@rateacompany.com')
            ->to('contact@rateacompany.com')
            ->subject($mail->getSubject())
            ->html($template);
        $this->mailer->send($email);
    }
    public function publicityPurchase(EmailEvent $event)
    {
        $mail = $event->getTemplateMail();
        $user = $event->getUser();
        $template = $mail->getMessage();
        $template = preg_replace('#\$user.firstname#', $user->getFirstname(), $template);
        $template = preg_replace('#\$user.lastname#', $user->getLastname(), $template);
        $template = preg_replace('#\$user.email#', $user->getEmail(), $template);
        $template = preg_replace('#\$user.civility#',  $user->getCivility() , $template);
        $email = (new Email())
            ->from('contact@rateacompany.com')
            ->to($user->getEmail())
            ->subject($mail->getSubject())
            ->html($template);
        $this->mailer->send($email);
    }
    public function newPublicitySearch(EmailEvent $event)
    {
        $mail = $event->getTemplateMail();
        $user = $event->getUser();
        $template = $mail->getMessage();
        $template = preg_replace('#\$user.username#', $user->getFirstname(), $template);
        $template = preg_replace('#\$user.email#', $user->getEmail(), $template);
        $template = preg_replace('#\$user.publicite#',  $event->getParam() , $template);
        $email = (new Email())
            ->from('contact@rateacompany.com')
            ->to('contact@rateacompany.com')
            ->subject($mail->getSubject())
            ->html($template);
        $this->mailer->send($email);
    }
    public function newAccountPro(EmailEvent $event)
    {
        $mail = $event->getTemplateMail();
        $user = $event->getUser();
        $template = $mail->getMessage();
        $template = preg_replace('#\$user.firstname#', $user->getFirstname(), $template);
        $template = preg_replace('#\$user.lastname#', $user->getLastname(), $template);
        $template = preg_replace('#\$user.url#', $event->getParam()->getName(), $template);
        $template = preg_replace('#\$user.civility#',  $user->getCivility() , $template);
        $email = (new Email())
            ->from('contact@rateacompany.com')
            ->to($user->getEmail())
            ->subject($mail->getSubject())
            ->html($template);
        $this->mailer->send($email);
    }
    public function AccountProDelete(EmailEvent $event)
    {
        $mail = $event->getTemplateMail();
        $user = $event->getUser();
        $template = $mail->getMessage();
        $template = preg_replace('#\$user.firstname#', $event->getParam()->getFirstname(), $template);
        $template = preg_replace('#\$user.lastname#', $event->getParam()->getLastname(), $template);
        $template = preg_replace('#\$user.civility#',  $event->getParam()->getCivility() , $template);
        $email = (new Email())
            ->from('contact@rateacompany.com')
            ->to('contact@rateacompany.com')
            ->subject($mail->getSubject())
            ->html($template);
        $this->mailer->send($email);
    }
}