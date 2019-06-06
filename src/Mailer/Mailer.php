<?php

namespace Drupal\wmmailable\Mailer;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Mail\MailManager;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\wmmailable\MailableInterface;
use Drupal\wmmailable\MailableManager;

class Mailer extends MailerBase
{
    use StringTranslationTrait;

    /** @var MailManager */
    protected $mailManager;

    public function __construct(
        MailableManager $mailableManager,
        LoggerChannelFactoryInterface $logger,
        MailManager $mailManager
    ) {
        parent::__construct($mailableManager, $logger);
        $this->mailManager = $mailManager;
    }

    public function send(string $id, array $parameters = []): bool
    {
        $mailable = $this->prepareMailable($id, $parameters);

        if (!$mailable instanceof MailableInterface) {
            return true;
        }

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
