<?php

namespace Drupal\wmmailable\Mailer;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\wmmailable\MailableInterface;
use Drupal\wmmailable\MailableManager;
use RuntimeException;

abstract class MailerBase implements MailerInterface
{
    use StringTranslationTrait;

    /** @var MailableManager */
    protected $mailableManager;

    public function __construct(
        MailableManager $mailableManager
    ) {
        $this->mailableManager = $mailableManager;
    }

    public function create(string $id): MailableInterface
    {
        if (!$this->mailableManager->hasDefinition($id)) {
            throw new RuntimeException(
                $this->t('No mailable found with id %id', ['%id' => $id])
            );
        }

        return $this->mailableManager->createInstance($id);
    }
}
