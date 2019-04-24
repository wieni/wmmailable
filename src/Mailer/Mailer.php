<?php

namespace Drupal\wmmailable\Mailer;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Mail\MailManager;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\wmmailable\Exception\DiscardMailException;
use Drupal\wmmailable\MailableInterface;
use Drupal\wmmailable\MailableManager;

class Mailer extends MailerBase
{
    use StringTranslationTrait;

    /** @var MailManager */
    protected $mailManager;
    /** @var MailableManager */
    protected $mailableManager;
    /** @var LoggerChannelFactoryInterface */
    protected $logger;

    public function __construct(
        MailManager $mailManager,
        MailableManager $mailableManager,
        LoggerChannelFactoryInterface $logger
    ) {
        $this->mailManager = $mailManager;
        $this->mailableManager = $mailableManager;
        $this->logger = $logger;
    }

    public function send(string $id, array $parameters = []): bool
    {
        if (!$this->mailableManager->hasDefinition($id)) {
            throw new \Exception(
                $this->t('No mailable found with id %id', ['%id' => $id])
            );
        }

        /** @var MailableInterface $mailable */
        $mailable = $this->mailableManager->createInstance($id);

        foreach ($this->recepients as $type => $emails) {
            foreach ($emails as $email) {
                $mailable->addRecepient($email, $type);
            }
        }

        if ($this->from) {
            $mailable->setFrom($this->from);
        }

        if ($this->replyTo) {
            $mailable->setReplyTo($this->replyTo);
        }

        try {
            $mailable = $mailable->build($parameters);
        } catch (DiscardMailException $e) {
            $this->logger->get('wmmailable')->debug(
                sprintf(
                    'Discarded mailable \'%s\'. Reason: %s',
                    $id,
                    empty($e->getMessage()) ? 'none' : $e->getMessage()
                )
            );

            return true;
        }

        $message = $this->mailManager->mail(
            'wmmailable',
            $id,
            null,
            null,
            compact('mailable')
        );

        return $message['result'];
    }
}
