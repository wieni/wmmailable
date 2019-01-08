<?php

namespace Drupal\wmmailable\Plugin\Mail;

use Drupal\Core\Mail\MailInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\Site\Settings;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a plugin to format mails
 *
 * @Mail(
 *   id = "mailable_plain",
 *   label = @Translation("Mailable - Plain"),
 *   description = @Translation("Basic mail formatter.")
 * )
 */
class PlainMailableFormatter implements MailInterface, ContainerFactoryPluginInterface
{
    /** @var MailManagerInterface */
    protected $mailManager;
    /** @var RendererInterface */
    protected $renderer;

    public function __construct(
        MailManagerInterface $mailManager,
        RendererInterface $renderer
    ) {
        $this->mailManager = $mailManager;
        $this->renderer = $renderer;
    }

    /**
     * {@inheritdoc}
     */
    public function format(array $message)
    {
        $contentType = 'text/plain';

        if (is_array($message['body'])) {
            $lineEndings = Settings::get('mail_line_endings', PHP_EOL);
            $message['body'] = implode($lineEndings, $message['body']);
        }

        if (
            !empty($message['headers']['Content-Type'])
            && preg_match('/.*\;/U', $message['headers']['Content-Type'], $matches)
        ) {
            $contentType = trim(substr($matches[0], 0, -1));
        }

        if ($contentType === 'text/html') {
            $this->formatHtml($message);
        } else {
            $this->formatPlain($message);
        }

        return $message;
    }

    protected function formatPlain(array &$message)
    {
        $body = (string) $message['body'];

        $body = html_entity_decode($body);
        $body = strip_tags($body);
        $body = trim($body);

        $message['body'] = $body;
    }

    protected function formatHtml(array &$message)
    {
        $render = [
            '#theme' => 'wmmailable',
            '#body' => $message['body'],
        ];

        $message['body'] = $this->renderer->renderPlain($render);
    }

    /**
     * {@inheritdoc}
     *
     * This plugin does not provide an implementation for sending mails,
     * so it uses the one configured in the mailsystem module.
     */
    public function mail(array $message)
    {
        $options = [
            'module' => $message['module'] ?? null,
            'key' => $message['key'] ?? null,
        ];

        return $this->mailManager
            ->getInstance($options)
            ->mail($message);
    }

    /**
     * {@inheritdoc}
     */
    public static function create(
        ContainerInterface $container,
        array $configuration,
        $plugin_id,
        $plugin_definition
    ) {
        return new static(
            $container->get('plugin.manager.mail'),
            $container->get('renderer')
        );
    }
}
