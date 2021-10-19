<?php

namespace Drupal\wmmailable;

use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\language\ConfigurableLanguageManagerInterface;
use Drupal\language\LanguageNegotiatorInterface;
use Drupal\wmmailable\Language\MailableLanguageNegotiator;

trait LanguageOverrideTrait
{
    /** @var LanguageManagerInterface */
    protected $languageManager;
    /** @var TranslationInterface */
    protected $translationManager;

    /** @var LanguageNegotiatorInterface */
    protected $originalNegotiator;
    /** @var LanguageNegotiatorInterface */
    protected $defaultNegotiator;
    /** @var MailableLanguageNegotiator */
    protected $customNegotiator;

    public function setDefaultLanguageNegotiator(LanguageNegotiatorInterface $languageNegotiator): void
    {
        $this->defaultNegotiator = $languageNegotiator;
    }

    public function setCustomLanguageNegotiator(MailableLanguageNegotiator $languageNegotiator): void
    {
        $this->customNegotiator = $languageNegotiator;
    }

    protected function overrideLanguage(string $langcode): void
    {
        if (!$this->languageManager instanceof ConfigurableLanguageManagerInterface) {
            return;
        }

        $this->originalNegotiator = $this->languageManager->getNegotiator();
        $this->customNegotiator->setOverride($langcode);
        $this->languageManager->setNegotiator($this->customNegotiator);
        $this->translationManager->setDefaultLangcode($langcode);
    }

    protected function restoreLanguage(): void
    {
        if (!$this->languageManager instanceof ConfigurableLanguageManagerInterface) {
            return;
        }

        $this->languageManager->setNegotiator($this->originalNegotiator ?? $this->defaultNegotiator);
        $this->translationManager->setDefaultLangcode($this->languageManager->getDefaultLanguage()->getId());
    }
}
