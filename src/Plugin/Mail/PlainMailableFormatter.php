<?php

namespace Drupal\wmmailable\Plugin\Mail;

use Drupal\Core\Mail\MailInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Render\RendererInterface;
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
        if (is_array($message['body'])) {
            $message['body'] = Markup::create(
                implode(PHP_EOL, $message['body'])
            );
        }

        $render = [
            '#theme' => 'wmmailable',
            '#body' => $message['body'],
        ];

        $message['body'] = $this->renderer->renderPlain($render);

        return $message;
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
