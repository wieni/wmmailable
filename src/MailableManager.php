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
}
