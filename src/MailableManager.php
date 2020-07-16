<?php

namespace Drupal\wmmailable;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\wmmailable\Annotation\Mailable;

class MailableManager extends DefaultPluginManager
{
    public function __construct(
        \Traversable $namespaces,
        CacheBackendInterface $cacheBackend,
        ModuleHandlerInterface $moduleHandler
    ) {
        parent::__construct(
            'Mail',
            $namespaces,
            $moduleHandler,
            MailableInterface::class,
            Mailable::class
        );
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
