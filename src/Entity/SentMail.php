<?php

namespace Drupal\wmmailable\Entity;

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\wmmailable\MailableInterface;

/**
 * @ContentEntityType(
 *     id = "sent_mail",
 *     label = @Translation("Sent mail"),
 *     handlers = {
 *         "list_builder" = "Drupal\wmmailable\ListBuilder\SentMailListBuilder",
 *     },
 *     base_table = "sent_mails",
 *     translatable = FALSE,
 *     entity_keys = {
 *         "id" : "smid",
 *     },
 * )
 *
 * @method static SentMail create(array $values = [])
 */
class SentMail extends ContentEntityBase implements MailableInterface
{
    public static function baseFieldDefinitions(EntityTypeInterface $entityType)
    {
        $fields['smid'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('Sent mail ID'))
            ->setReadOnly(true);

        $fields['created'] = BaseFieldDefinition::create('timestamp')
            ->setLabel(t('Created on'))
            ->setDescription(t('The time that the mail was created.'))
            ->setDisplayOptions('form', [
                'type' => 'datetime_timestamp',
            ]);

        $fields['sent'] = BaseFieldDefinition::create('created')
            ->setLabel(t('Sent on'))
            ->setDescription(t('The time that the mail was attempted to be sent.'))
            ->setDisplayOptions('form', [
                'type' => 'datetime_timestamp',
            ]);

        $fields['language'] = BaseFieldDefinition::create('language')
            ->setLabel(t('Language'))
            ->setDisplayOptions('form', [
                'type' => 'language_select',
            ]);

        $fields['module'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Module'));

        $fields['key'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Key'));

        $fields['template'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Template'));

        $fields['subject'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Subject'));

        $fields['from'] = BaseFieldDefinition::create('email')
            ->setLabel(t('From'));

        $fields['reply_to'] = BaseFieldDefinition::create('email')
            ->setLabel(t('Reply to'));

        $fields['parameters'] = BaseFieldDefinition::create('map')
            ->setLabel(t('Parameters'));

        $fields['libraries'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Libraries'))
            ->setCardinality(-1);

        $fields['recepient_to'] = BaseFieldDefinition::create('string')
            ->setLabel(t('To'))
            ->setCardinality(-1);

        $fields['recepient_cc'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Cc'))
            ->setCardinality(-1);

        $fields['recepient_bcc'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Bcc'))
            ->setCardinality(-1);

        $fields['headers'] = BaseFieldDefinition::create('map')
            ->setLabel(t('Headers'));

        $fields['content_type'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Content type'));

        $fields['charset'] = BaseFieldDefinition::create('string')
            ->setLabel(t('Charset'));

        $fields['result'] = BaseFieldDefinition::create('boolean')
            ->setLabel(t('Result'))
            ->setRequired(false);

        return $fields;
    }

    public static function fromMessage(array $message): SentMail
    {
        if ($message['result'] !== null && !is_bool($message['result'])) {
            $message['result'] = true;
        }

        $values = [
            'created' => $message['created'],
            'language' => $message['langcode'],
            'module' => $message['module'],
            'key' => $message['key'],
            'template' => null,
            'subject' => $message['subject'],
            'from' => $message['from'],
            'reply_to' => $message['reply-to'],
            'parameters' => $message['params'],
            'libraries' => null,
            'recepient_to' => $message['to'],
            'recepient_cc' => null,
            'recepient_bcc' => null,
            'headers' => $message['headers'],
            'content_type' => null,
            'charset' => null,
            'result' => $message['result'],
        ];

        if (isset($message['mailable']) && $message['mailable'] instanceof MailableInterface) {
            $values['template'] = $message['mailable']->getTemplate();
            $values['parameters'] = $message['mailable']->getParameters();
            $values['libraries'] = $message['mailable']->getLibraries();
            $values['recepient_cc'] = $message['mailable']->getCc();
            $values['recepient_bcc'] = $message['mailable']->getBcc();
            $values['content_type'] = $message['mailable']->getContentType();
            $values['charset'] = $message['mailable']->getCharset();
        }

        return self::create($values);
    }

    public function getCreatedTime(): int
    {
        return $this->get('created')->value;
    }

    public function setCreatedTime(int $timestamp): MailableInterface
    {
        return $this->set('created', $timestamp);
    }

    public function getCreated(): ?\DateTimeInterface
    {
        try {
            return new \DateTime($this->get('created')->value);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function setCreated(\DateTimeInterface $dateTime): MailableInterface
    {
        return $this->set('created', $dateTime->format('U'));
    }

    public function getSentTime(): int
    {
        return $this->get('sent')->value;
    }

    public function setSentTime(int $timestamp): MailableInterface
    {
        return $this->set('sent', $timestamp);
    }

    public function getSent(): ?\DateTimeInterface
    {
        try {
            return new \DateTime($this->get('sent')->value);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function setSent(\DateTimeInterface $dateTime): MailableInterface
    {
        return $this->set('sent', $dateTime->format('U'));
    }

    public function getKey(): string
    {
        return $this->get('key')->value;
    }

    public function getModule(): string
    {
        return $this->get('module')->value;
    }

    public function getTemplate(): string
    {
        return $this->get('template')->value;
    }

    public function getSubject(): string
    {
        return $this->get('subject')->value;
    }

    public function setSubject(string $subject): MailableInterface
    {
        return $this->set('subject', $subject);
    }

    public function getFrom(): string
    {
        return $this->get('from')->value;
    }

    public function setFrom(string $from): MailableInterface
    {
        return $this->set('from', $from);
    }

    public function getReplyTo(): string
    {
        return $this->get('reply_to')->value;
    }

    public function setReplyTo(string $replyTo): MailableInterface
    {
        return $this->set('reply_to', $replyTo);
    }

    public function getLangcode(): string
    {
        return $this->get('language')->value;
    }

    public function setLangcode(string $langcode): MailableInterface
    {
        return $this->set('language', $langcode);
    }

    public function getParameters(): array
    {
        return $this->get('parameters')->value;
    }

    public function setParameters(array $parameters): MailableInterface
    {
        return $this->set('parameters', $parameters);
    }

    public function getLibraries(): array
    {
        return array_column(
            $this->get('libraries')->getValue(),
            'value'
        );
    }

    public function setLibraries(array $libraries): MailableInterface
    {
        return $this->set('libraries', $libraries);
    }

    public function addLibrary(string $library): MailableInterface
    {
        $this->get('libraries')->appendItem($library);

        return $this;
    }

    public function getRecepients(string $type = self::RECEPIENT_TO): array
    {
        switch($type) {
            case self::RECEPIENT_TO:
                return array_column(
                    $this->get('recepient_to')->getValue(),
                    'value'
                );
            case self::RECEPIENT_CC:
                return $this->getCc();
            case self::RECEPIENT_BCC:
                return $this->getBcc();
            default:
                return [];
        }
    }

    public function setRecepients(array $recepients, string $type = self::RECEPIENT_TO): MailableInterface
    {
        switch($type) {
            case self::RECEPIENT_TO:
                return $this->set('recepient_to', $recepients);
            case self::RECEPIENT_CC:
                return $this->setCc($recepients);
            case self::RECEPIENT_BCC:
                return $this->setBcc($recepients);
            default:
                return $this;
        }
    }

    public function addRecepient(string $recepient, string $type = self::RECEPIENT_TO): MailableInterface
    {
        switch($type) {
            case self::RECEPIENT_TO:
                $this->get('recepient_to')->appendItem($recepient);
                return $this;
            case self::RECEPIENT_CC:
                return $this->addCc($recepient);
            case self::RECEPIENT_BCC:
                return $this->addBcc($recepient);
            default:
                return $this;
        }
    }

    public function getCc(): array
    {
        return array_column(
            $this->get('recepient_cc')->getValue(),
            'value'
        );
    }

    public function setCc(array $recepients): MailableInterface
    {
        return $this->set('recepient_cc', $recepients);
    }

    public function addCc(string $recepient): MailableInterface
    {
        $this->get('recepient_cc')->appendItem($recepient);

        return $this;
    }

    public function getBcc(): array
    {
        return array_column(
            $this->get('recepient_bcc')->getValue(),
            'value'
        );
    }

    public function setBcc(array $recepients): MailableInterface
    {
        return $this->set('recepient_bcc', $recepients);
    }

    public function addBcc(string $recepient): MailableInterface
    {
        $this->get('recepient_bcc')->appendItem($recepient);

        return $this;
    }

    public function getHeaders(): array
    {
        return $this->get('headers')->value;
    }

    public function getHeader(string $name): string
    {
        return $this->getHeaders()[$name] ?? '';
    }

    public function setHeader(string $name, string $header): MailableInterface
    {
        $headers = $this->getHeaders();
        $headers[$name] = $header;

        return $this->set('headers', $headers);
    }

    public function getContentType(): ?string
    {
        return $this->get('content_type')->value;
    }

    public function setContentType(string $contentType): MailableInterface
    {
        return $this->set('content_type', $contentType);
    }

    public function getCharset(): ?string
    {
        return $this->get('charset')->value;
    }

    public function setCharset(string $charset): MailableInterface
    {
        return $this->set('charset', $charset);
    }

    public function getResult(): bool
    {
        return $this->get('result')->value;
    }

    public function setResult(bool $result): MailableInterface
    {
        return $this->set('result', $result);
    }

    public function getStatus(): MarkupInterface
    {
        if ($this->getResult() === null) {
            return t('Not sent');
        }

        if ($this->getResult() === false) {
            return t('Error');
        }

        return t('Sent');
    }
}
