<?php

namespace Drupal\wmmailable\Mailer;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Core\Theme\ThemeInitializationInterface;
use Drupal\Core\Theme\ThemeManagerInterface;
use Drupal\wmmailable\LanguageOverrideTrait;
use Drupal\wmmailable\MailableInterface;
use Drupal\wmmailable\MailableManager;
use Drupal\wmmailable\ThemeOverrideTrait;
use RuntimeException;

abstract class MailerBase implements MailerInterface
{
    use StringTranslationTrait;
    use LanguageOverrideTrait;
    use ThemeOverrideTrait;

    /** @var MailableManager */
    protected $mailableManager;

    public function __construct(
        LanguageManagerInterface $languageManager,
        TranslationInterface $translationManager,
        ConfigFactoryInterface $configFactory,
        ThemeManagerInterface $themeManager,
        ThemeInitializationInterface $themeInitialization,
        MailableManager $mailableManager
    ) {
        $this->languageManager = $languageManager;
        $this->translationManager = $translationManager;
        $this->configFactory = $configFactory;
        $this->themeManager = $themeManager;
        $this->themeInitialization = $themeInitialization;
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
