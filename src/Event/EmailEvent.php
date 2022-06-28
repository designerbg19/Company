<?php

namespace App\Event;

use App\Entity\Mail;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class EmailEvent extends Event
{
    const COMPANY = "company.create";
    const USER = "user.create";
    const ADMIBUSER = "user.admin";
    const PUBLICITY = "purchase.publicity";
    const NEWPUBLICITYSEARCH = "new.publicity.search";
    const ACCOUNTPRO = "new.account.pro";
    const ACCOUNTPRODELETE = "account.pro.delete";
    /**
     * @var Mail $mail
     */
    private $mail;
    /**
     * @var User $user
     */
    private $user;

    /**
     * @var $param
     */
    private $param;

    public function __construct(Mail $mail,User $user,$param)
    {
        $this->mail = $mail;
        $this->user = $user;
        $this->param = $param;
    }

    public function getTemplateMail()
    {
        return $this->mail;
    }
    public function getUser()
    {
        return $this->user;
    }

    public function getParam()
    {
        return $this->param;
    }
}