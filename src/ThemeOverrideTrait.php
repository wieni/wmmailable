<?php

namespace Drupal\wmmailable;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Theme\ActiveTheme;
use Drupal\Core\Theme\ThemeInitializationInterface;
use Drupal\Core\Theme\ThemeManagerInterface;

trait ThemeOverrideTrait
{
    /** @var ConfigFactoryInterface */
    protected $configFactory;
    /** @var ThemeManagerInterface */
    protected $themeManager;
    /** @var ThemeInitializationInterface */
    protected $themeInitialization;

    /** @var ActiveTheme */
    protected $originalActiveTheme;

    protected function overrideTheme(): void
    {
        $themeName = $this->configFactory->get('wmmailable.settings')->get('theme');
        $activeTheme = $this->themeManager->getActiveTheme();

        if ($activeTheme->getName() !== $themeName) {
            $this->originalActiveTheme = $activeTheme;
            $this->themeManager->setActiveTheme($this->themeInitialization->initTheme($themeName));
        }
    }

    protected function restoreTheme(): void
    {
        if (!isset($this->originalActiveTheme)) {
            return;
        }

        $this->themeManager->setActiveTheme($this->originalActiveTheme);
    }
}
