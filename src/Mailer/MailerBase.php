<?php

namespace Drupal\wmmailable\Mailer;

use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\wmmailable\LanguageOverrideTrait;
use Drupal\wmmailable\MailableInterface;
use Drupal\wmmailable\MailableManager;
use RuntimeException;

abstract class MailerBase implements MailerInterface
{
    use StringTranslationTrait;
    use LanguageOverrideTrait;

    /** @var MailableManager */
    protected $mailableManager;

    public function __construct(
        LanguageManagerInterface $languageManager,
        TranslationInterface $translationManager,
        MailableManager $mailableManager
    ) {
        $this->languageManager = $languageManager;
        $this->translationManager = $translationManager;
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
