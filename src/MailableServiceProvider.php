<?php

namespace Drupal\wmmailable;

use Drupal\Core\Config\BootstrapConfigStorageFactory;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceModifierInterface;

class MailableServiceProvider implements ServiceModifierInterface
{
    public function alter(ContainerBuilder $container)
    {
        $config = BootstrapConfigStorageFactory::get()->read('wmmailable.settings');

        if (!empty($config['mailer'])) {
            $container->setDefinition('wmmailable.mailer', $container->getDefinition($config['mailer']));
        }
    }
}
