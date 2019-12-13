<?php

namespace Drupal\wmmailable\Mailer;

use Drupal\wmmailable\MailableInterface;

interface MailerInterface
{
    public function create(string $id): MailableInterface;

    public function send(MailableInterface $mail): bool;
}
