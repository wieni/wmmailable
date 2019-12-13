<?php

namespace Drupal\wmmailable\Mailer;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Mail\MailManager;
use Drupal\wmmailable\Exception\DiscardMailException;
use Drupal\wmmailable\MailableInterface;
use Drupal\wmmailable\MailableManager;

class Mailer extends MailerBase
{
    /** @var LoggerChannelFactoryInterface */
    protected $logger;
    /** @var MailManager */
    protected $mailManager;

    public function __construct(
        MailableManager $mailableManager,
        LoggerChannelFactoryInterface $logger,
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
            $this->logger->get('wmmailable')->debug(
                sprintf(
                    'Discarded mailable \'%s\'. Reason: %s',
                    $mailable->getKey(),
                    empty($e->getMessage()) ? 'none' : $e->getMessage()
                )
            );

            return null;
        }

        $message = $this->mailManager->mail(
            'wmmailable',
            $mailable->getKey(),
            null,
            null,
            compact('mailable')
        );

        return $message['result'];
    }
}
