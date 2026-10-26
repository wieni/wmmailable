<?php

namespace Drupal\wmmailable;

use Drupal\Component\Plugin\Attribute\Plugin;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\wmmailable\Annotation\Mailable;
use Drupal\wmmailable\Attribute\Mailable as MailableAttribute;

class MailableManager extends DefaultPluginManager
{
    public function __construct(
        \Traversable $namespaces,
        CacheBackendInterface $cacheBackend,
        ModuleHandlerInterface $moduleHandler
    ) {
        // Attribute discovery (and this constructor signature) only exists
        // since Drupal 10.2.
        if (class_exists(Plugin::class)) {
            parent::__construct(
                'Mail',
                $namespaces,
                $moduleHandler,
                MailableInterface::class,
                MailableAttribute::class,
                Mailable::class
            );
        } else {
            parent::__construct(
                'Mail',
                $namespaces,
                $moduleHandler,
                MailableInterface::class,
                Mailable::class
            );
        }
        $this->alterInfo('wmmailable_info');
        $this->setCacheBackend($cacheBackend, 'wmmailable_info_plugins');
    }

    protected function findDefinitions()
    {
        $definitions = parent::findDefinitions();

        foreach ($definitions as $id => $definition) {
            // Trim leading backslashes if the class is used as id
            if (class_exists($id)) {
                $newId = ltrim($id, '\\');
                $definition['id'] = $newId;
                unset($definitions[$id]);
                $definitions[$newId] = $definition;
            }
        }

        return $definitions;
    }
}
