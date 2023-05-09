<?php

namespace Drupal\wmmailable;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

class SentMailCleaner
{
    use StringTranslationTrait;

    /** @var EntityTypeManagerInterface */
    protected $entityTypeManager;
    /** @var ConfigFactoryInterface */
    protected $configFactory;
    /** @var LoggerChannelInterface */
    protected $logger;

    public function __construct(
        EntityTypeManagerInterface $entityTypeManager,
        ConfigFactoryInterface $configFactory,
        LoggerChannelInterface $logger
    ) {
        $this->entityTypeManager = $entityTypeManager;
        $this->configFactory = $configFactory;
        $this->logger = $logger;
    }

    public function clean()
    {
        $storage = $this->entityTypeManager
            ->getStorage('sent_mail');
        $ids = $storage->getQuery()
            ->condition('sent', time() - $this->getRetention(), '<')
            ->accessCheck(false)
            ->execute();

        if (empty($ids)) {
            return;
        }

        $storage->delete(
            $storage->loadMultiple($ids)
        );

        $this->logger->info($this->formatPlural(
            count($ids),
            'Cleaned up @count sent mail.',
            'Cleaned up @count sent mails.'
        ));
    }

    protected function getRetention(): int
    {
        $retention = $this->configFactory
            ->get('wmmailable.settings')
            ->get('sent_mail_retention');

        return $retention ?? (30 * 24 * 60 * 60);
    }
}
