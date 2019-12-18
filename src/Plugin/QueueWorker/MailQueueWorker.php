<?php

namespace Drupal\wmmailable\Plugin\QueueWorker;

use Drupal\Component\Render\PlainTextOutput;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @QueueWorker(
 *     id = \Drupal\wmmailable\Mailer\QueuedMailer::QUEUE_ID,
 *     title = @Translation("Mail queue"),
 *     cron = {"time" : 30}
 * )
 */
class MailQueueWorker extends QueueWorkerBase implements ContainerFactoryPluginInterface
{
    /** @var MailManagerInterface */
    protected $mailManager;

    public function __construct(
        array $configuration,
        $pluginId,
        $pluginDefinition,
        MailManagerInterface $mailManager
    ) {
        parent::__construct($configuration, $pluginId, $pluginDefinition);
        $this->mailManager = $mailManager;
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
            $container->get('plugin.manager.mail')
        );
    }

    /** Taken from @see MailManager::doMail */
    public function processItem($item)
    {
        $message = $item['message'];

        // Retrieve the responsible implementation for this message.
        $system = $this->mailManager->getInstance([
            'module' => $message['module'],
            'key' => $message['key'],
        ]);

        // Ensure that subject is plain text. By default translated and
        // formatted strings are prepared for the HTML context and email
        // subjects are plain strings.
        if ($message['subject']) {
            $message['subject'] = PlainTextOutput::renderFromHtml($message['subject']);
        }

        $message['result'] = $system->mail($message);

        if (!$message['result']) {
            throw new \Exception('Error while trying to send queued mailable.');
        }
    }
}
