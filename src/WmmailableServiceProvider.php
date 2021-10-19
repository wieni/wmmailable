<?php

namespace Drupal\wmmailable;

use Drupal\Core\Config\BootstrapConfigStorageFactory;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceModifierInterface;

class WmmailableServiceProvider implements ServiceModifierInterface
{
    public function alter(ContainerBuilder $container)
    {
        $config = BootstrapConfigStorageFactory::get()->read('wmmailable.settings');

        if (!empty($config['mailer']) && $container->hasDefinition($config['mailer'])) {
            $container->setDefinition('wmmailable.mailer', $container->getDefinition($config['mailer']));
        }

        if (
            !$container->has('language_negotiator')
            || !$container->has('plugin.manager.language_negotiation_method')
        ) {
            $container->removeDefinition('wmmailable.language_negotiator');
        }
    }
}
