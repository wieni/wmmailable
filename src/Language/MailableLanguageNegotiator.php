<?php

namespace Drupal\wmmailable\Language;

use Drupal\language\LanguageNegotiator;

class MailableLanguageNegotiator extends LanguageNegotiator
{
    /** @var string|null */
    protected $overrideLangcode;

    public function initializeType($type)
    {
        if (!isset($this->overrideLangcode)) {
            return parent::initializeType($type);
        }

        if (!$language = $this->languageManager->getLanguage($this->overrideLangcode)) {
            return parent::initializeType($type);
        }

        return [static::METHOD_ID => $language];
    }

    public function setOverride(string $langcode): void
    {
        $this->overrideLangcode = $langcode;
    }
}
