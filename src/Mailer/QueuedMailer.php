<?php

namespace Drupal\wmmailable\Mailer;

use Drupal\Core\Queue\QueueFactory;
use Drupal\Core\Queue\QueueInterface;

class QueuedMailer extends MailerBase implements MailerInterface
{
    const QUEUE_ID = 'wmmailable_mail';

    /** @var QueueInterface */
    protected $queue;

    public function __construct(
        QueueFactory $queueFactory
    ) {
        $this->queue = $queueFactory->get(self::QUEUE_ID);
    }

    public function send(string $id, array $parameters = []): bool
    {
        return $this->queue->createItem([
            'id' => $id,
            'parameters' => $parameters,
            'recipients' => $this->recepients,
        ]);
    }
}
