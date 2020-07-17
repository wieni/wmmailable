<?php

namespace Drupal\wmmailable\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * @Annotation
 */
class Mailable extends Plugin
{
    /** @var string */
    protected $template;
    /** @var string */
    protected $module = 'wmmailable';

    public function getId()
    {
        return $this->definition['id'] ?? $this->getClass();
    }
}
