<?php

namespace Drupal\wmmailable\Mailer;

use Drupal\Core\Mail\MailManager;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\wmmailable\MailableInterface;
use Drupal\wmmailable\MailableManager;

class Mailer extends MailerBase implements MailerInterface
{
    use StringTranslationTrait;

    /** @var MailManager */
    protected $mailManager;
    /** @var MailableManager */
    protected $mailableManager;

    public function __construct(
        MailManager $mailManager,
        MailableManager $mailableManager
    ) {
        $this->mailManager = $mailManager;
        $this->mailableManager = $mailableManager;
    }

    public function send(string $id, array $parameters = []): bool
    {
        if (!$this->mailableManager->hasDefinition($id)) {
            throw new \Exception(
                $this->t('No mailable found with id %id', ['%id' => $id])
            );
        }

        /** @var MailableInterface $mailable */
        $mailable = $this->mailableManager->createInstance($id);

        foreach ($this->recepients as $type => $emails) {
            foreach ($emails as $email) {
                $mailable->addRecepient($email, $type);
            }
        }

        $mailable = $mailable->build($parameters);

        $message = $this->mailManager->mail(
            'wmmailable',
            $id,
            null,
            null,
            compact('mailable')
        );

        return $message['result'];
    }
}
