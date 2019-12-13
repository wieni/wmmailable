<?php

namespace Drupal\wmmailable\Mailer;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Mail\MailManager;
use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Queue\QueueInterface;
use Drupal\wmmailable\Exception\DiscardMailException;
use Drupal\wmmailable\MailableInterface;
use Drupal\wmmailable\MailableManager;

class QueuedMailer extends MailerBase
{
    const QUEUE_ID = 'wmmailable_mail';

    /** @var LoggerChannelFactoryInterface */
    protected $logger;
    /** @var MailManager */
    protected $mailManager;
    /** @var QueueInterface */
    protected $queue;

    public function __construct(
        MailableManager $mailableManager,
        LoggerChannelFactoryInterface $logger,
        MailManager $mailManager,
        QueueFactory $queueFactory
    ) {
        parent::__construct($mailableManager);
        $this->logger = $logger;
        $this->mailManager = $mailManager;
        $this->queue = $queueFactory->get(self::QUEUE_ID);
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
            compact('mailable'),
            null,
            false
        );

        return $this->queue->createItem([
            'message' => $message,
        ]);
    }
}
