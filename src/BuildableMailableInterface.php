<?php

namespace Drupal\wmmailable;

use Drupal\wmmailable\Exception\DiscardMailException;

interface BuildableMailableInterface extends MailableInterface
{
    /** @throws DiscardMailException if the mail should be discarded */
    public function build(): MailableInterface;
}
