<?php

namespace Drupal\wmmailable\Plugin\QueueWorker;

use Drupal\Component\Render\PlainTextOutput;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\wmmailable\LanguageOverrideTrait;
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
    use LanguageOverrideTrait;

    /** @var MailManagerInterface */
    protected $mailManager;

    public static function create(
        ContainerInterface $container,
        array $configuration,
        $pluginId, $pluginDefinition
    ) {
        $instance = new static($configuration, $pluginId, $pluginDefinition);
        $instance->mailManager = $container->get('plugin.manager.mail');

        $instance->languageManager = $container->get('language_manager');
        $instance->translationManager = $container->get('string_translation');

        if ($container->has('language_negotiator')) {
            $instance->setDefaultLanguageNegotiator($container->get('language_negotiator'));
        }

        if ($container->has('wmmailable.language_negotiator')) {
            $instance->setCustomLanguageNegotiator($container->get('wmmailable.language_negotiator'));
        }

        return $instance;
    }

    /** Taken from @see MailManager::doMail */
    public function processItem($item)
    {
        $message = $item['message'];
        $overrideLanguage = isset($message['langcode']);

        if ($overrideLanguage) {
            $this->overrideLanguage($message['langcode']);
        }

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

        if ($overrideLanguage) {
            $this->restoreLanguage();
        }

        if (!$message['result']) {
            throw new \Exception('Error while trying to send queued mailable.');
        }
    }
}
