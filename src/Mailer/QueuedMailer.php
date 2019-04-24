<?php

namespace Drupal\wmmailable\Mailer;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Mail\MailManager;
use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Queue\QueueInterface;
use Drupal\wmmailable\MailableInterface;
use Drupal\wmmailable\MailableManager;

class QueuedMailer extends MailerBase
{
    const QUEUE_ID = 'wmmailable_mail';

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
        parent::__construct($mailableManager, $logger);
        $this->mailManager = $mailManager;
        $this->queue = $queueFactory->get(self::QUEUE_ID);
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
            compact('mailable'),
            null,
            false
        );

        return $this->queue->createItem([
            'message' => $message,
        ]);
    }
}
