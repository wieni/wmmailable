<?php

namespace Drupal\wmmailable\Plugin\Mail;

use Drupal\Core\Mail\MailInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class MailableFormatterBase implements MailInterface, ContainerFactoryPluginInterface
{
    /** @var MailManagerInterface */
    protected $mailManager;

    public function __construct(
        MailManagerInterface $mailManager
    ) {
        $this->mailManager = $mailManager;
    }

    public static function create(
        ContainerInterface $container,
        array $configuration,
        $plugin_id,
        $plugin_definition
    ) {
        return new static(
            $container->get('plugin.manager.mail')
        );
    }

    abstract public function format(array $message);

    /**
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
}
