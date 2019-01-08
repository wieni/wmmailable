<?php

namespace Drupal\wmmailable\Event;

use Drupal\wmmailable\MailableInterface;
use Symfony\Component\EventDispatcher\Event;

class MailableAlterEvent extends Event
{
    /** @var MailableInterface */
    protected $mailable;

    public function __construct(
        MailableInterface $mailable
    ) {
        $this->mailable = $mailable;
    }

    public function getMailable(): MailableInterface
    {
        return $this->mailable;
    }
}
