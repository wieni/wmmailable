<?php

namespace Drupal\wmmailable\Mailer;

use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Mail\MailManager;
use Drupal\wmmailable\Exception\DiscardMailException;
use Drupal\wmmailable\MailableInterface;
use Drupal\wmmailable\MailableManager;

class Mailer extends MailerBase
{
    /** @var LoggerChannelInterface */
    protected $logger;
    /** @var MailManager */
    protected $mailManager;

    public function __construct(
        MailableManager $mailableManager,
        LoggerChannelInterface $logger,
        MailManager $mailManager
    ) {
        parent::__construct($mailableManager);
        $this->logger = $logger;
        $this->mailManager = $mailManager;
    }

    public function send(MailableInterface $mailable): bool
    {
        try {
            $mailable->build();
        } catch (DiscardMailException $e) {
            $this->logger->debug(
                sprintf(
                    'Discarded mailable \'%s\'. Reason: %s',
                    $mailable->getKey(),
                    empty($e->getMessage()) ? 'none' : $e->getMessage()
                )
            );

            return false;
        }

        $message = $this->mailManager->mail(
            'wmmailable',
            $mailable->getKey(),
            null,
            null,
            compact('mailable')
        );

        return !empty($message['result']);
    }
}
