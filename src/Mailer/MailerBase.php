<?php

namespace Drupal\wmmailable\Mailer;

use Drupal\wmmailable\MailableInterface;

abstract class MailerBase implements MailerInterface
{
    /** @var string */
    protected $from;
    /** @var string */
    protected $replyTo;
    /** @var array */
    protected $recepients = [
        MailableInterface::RECEPIENT_TO => [],
        MailableInterface::RECEPIENT_CC => [],
        MailableInterface::RECEPIENT_BCC => [],
    ];

    public function to(array $to): MailerInterface
    {
        $this->recepients[MailableInterface::RECEPIENT_TO] = $to;
        return $this;
    }

    public function cc(array $cc): MailerInterface
    {
        $this->recepients[MailableInterface::RECEPIENT_CC] = $cc;
        return $this;
    }

    public function bcc(array $bcc): MailerInterface
    {
        $this->recepients[MailableInterface::RECEPIENT_BCC] = $bcc;
        return $this;
    }

    public function from(string $from): MailerInterface
    {
        $this->from = $from;
        return $this;
    }

    public function replyTo(string $replyTo): MailerInterface
    {
        $this->replyTo = $replyTo;
        return $this;
    }
}
