<?php

namespace Drupal\wmmailable\ListBuilder;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\wmmailable\Entity\SentMail;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SentMailListBuilder extends EntityListBuilder
{
    /** @var LanguageManagerInterface */
    protected $languageManager;
    /** @var DateFormatterInterface */
    protected $dateFormatter;

    public static function createInstance(
        ContainerInterface $container,
        EntityTypeInterface $entityType
    ) {
        $instance = parent::createInstance($container, $entityType);
        $instance->languageManager = $container->get('language_manager');
        $instance->dateFormatter = $container->get('date.formatter');

        return $instance;
    }

    public function buildHeader()
    {
        return [
            'date' => $this->t('Date created'),
            'status' => $this->t('Status'),
            'module' => $this->t('Module'),
            'key' => $this->t('Key'),
            'from' => $this->t('From'),
            'recepient_to' => $this->t('To'),
            'language' => $this->t('Language'),
        ];
    }

    public function buildRow(EntityInterface $entity)
    {
        assert($entity instanceof SentMail);

        $values = [
            'date' => null,
            'status' => $entity->getStatus(),
            'module' => $entity->getModule(),
            'key' => $entity->getKey(),
            'from' => $entity->getFrom(),
            'recepient_to' => implode(', ', $entity->getRecepients()),
            'language' => $this->languageManager->getLanguageName($entity->getLangcode()),
        ];

        if ($entity->getSentTime()) {
            $values['date'] = $this->t('@createdDate (sent @sentDate later)', [
                '@createdDate' => $this->dateFormatter->format($entity->getCreatedTime(), 'short'),
                '@sentDate' => $this->dateFormatter->formatDiff($entity->getCreatedTime(), $entity->getSentTime()),
            ]);
        } else {
            $values['date'] = $this->dateFormatter->format($entity->getCreatedTime(), 'short');
        }

        return $values;
    }

    public function render()
    {
        $build = parent::render();
        $build['table']['#empty'] = $this->t('There are currently no sent mails.');

        return $build;
    }
}
