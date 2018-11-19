<?php

namespace Drupal\wmmailable\Mailer;

interface MailerInterface
{
    public function to(array $to): MailerInterface;

    public function cc(array $cc): MailerInterface;

    public function bcc(array $bcc): MailerInterface;

    public function send(string $id, array $parameters = []): bool;
}
