<?php

namespace Drupal\wmmailable\Plugin\Mail;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Mail\MailInterface;
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\wmmailable\Entity\SentMail;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a plugin to log mails after sending.
 *
 * @Mail(
 *     id = "mailable_log",
 *     label = @Translation("Mailable - Logger"),
 *     description = @Translation("Mail formatter which logs mails after sending them.")
 * )
 */
class MailLogger implements MailInterface, ContainerFactoryPluginInterface
{
    /** @var ConfigFactoryInterface */
    protected $configFactory;
    /** @var MailManagerInterface */
    protected $mailManager;

    public function __construct(
        ConfigFactoryInterface $configFactory,
        MailManagerInterface $mailManager
    ) {
        $this->configFactory = $configFactory;
        $this->mailManager = $mailManager;
    }

    public static function create(
        ContainerInterface $container,
        array $configuration,
        $plugin_id,
        $plugin_definition
    ) {
        return new static(
            $container->get('config.factory'),
            $container->get('plugin.manager.mail')
        );
    }

    /**
     * This plugin does not provide an implementation for formatting mails,
     * so it uses the one configured in the mailsystem module.
     */
    public function format(array $message)
    {
        $options = [
            'module' => $message['module'] ?? null,
            'key' => $message['key'] ?? null,
        ];

        return $this->mailManager
            ->getInstance($options)
            ->format($message);
    }

    /**
     * This plugin does not provide an implementation for sending mails,
     * so it uses the one configured in the wmmailable settings.
     */
    public function mail(array $message)
    {
        $pluginId = $this->configFactory
                ->get('wmmailable.settings')
                ->get('sender') ?? 'php_mail';

        $result = $this->mailManager
            ->createInstance($pluginId)
            ->mail($message);

        SentMail::fromMessage($message + ['result' => $result])->save();

        return $result;
    }
}
