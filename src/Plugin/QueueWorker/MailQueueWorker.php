<?php

namespace Drupal\wmmailable\Plugin\QueueWorker;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\wmmailable\MailableInterface;
use Drupal\wmmailable\Mailer\MailerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @QueueWorker(
 *   id = \Drupal\wmmailable\Mailer\QueuedMailer::QUEUE_ID,
 *   title = @Translation("Mail queue"),
 *   cron = {"time" = 30}
 * )
 */
class MailQueueWorker extends QueueWorkerBase implements ContainerFactoryPluginInterface
{
    /** @var MailerInterface */
    protected $mailer;

    public function __construct(
        array $configuration,
        $pluginId,
        $pluginDefinition,
        MailerInterface $mailer
    ) {
        parent::__construct($configuration, $pluginId, $pluginDefinition);
        $this->mailer = $mailer;
    }

    public static function create(
        ContainerInterface $container,
        array $configuration,
        $pluginId,
        $pluginDefinition
    ) {
        return new static(
            $configuration,
            $pluginId,
            $pluginDefinition,
            $container->get('wmmailable.mailer.direct')
        );
    }

    public function processItem($item)
    {
        $result = $this->mailer
            ->to($item['recipients'][MailableInterface::RECEPIENT_TO] ?? [])
            ->cc($item['recipients'][MailableInterface::RECEPIENT_CC] ?? [])
            ->bcc($item['recipients'][MailableInterface::RECEPIENT_BCC] ?? [])
            ->send($item['id'], $item['parameters']);

        if (!$result) {
            throw new \Exception('Error while trying to send queued mailable.');
        }
    }
}
