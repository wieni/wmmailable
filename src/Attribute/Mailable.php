<?php

namespace Drupal\wmmailable\Attribute;

use Drupal\Component\Plugin\Attribute\Plugin;

/**
 * Attribute counterpart of the Mailable annotation.
 *
 * Takes the same properties, with the same defaults. Like the annotation,
 * the plugin ID defaults to the class name.
 *
 * @see \Drupal\wmmailable\Annotation\Mailable
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Mailable extends Plugin
{
    public function __construct(
        ?string $id = null,
        public readonly ?string $template = null,
        public readonly string $module = 'wmmailable',
        ?string $deriver = null,
    ) {
        // The class name is only known after construction, see getId().
        parent::__construct($id ?? '', $deriver);
    }

    public function getId(): string
    {
        return $this->id !== '' ? $this->id : $this->getClass();
    }

    public function get(): array
    {
        $definition = parent::get();

        // Like the annotation, leave out an ID that wasn't given.
        if ($definition['id'] === '') {
            unset($definition['id']);
        }

        return $definition;
    }
}
