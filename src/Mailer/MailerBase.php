<?php

namespace Drupal\wmmailable\Mailer;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\wmmailable\Exception\DiscardMailException;
use Drupal\wmmailable\MailableInterface;
use Drupal\wmmailable\MailableManager;

abstract class MailerBase implements MailerInterface
{
    /** @var string */
    protected $from;
    /** @var string */
    protected $replyTo;
    /** @var array */
    protected $recepients = [
        MailableInterface::RECEPIENT_TO => [],
        MailableInterface::RECEPIENT_CC => [],
        MailableInterface::RECEPIENT_BCC => [],
    ];

    /** @var MailableManager */
    protected $mailableManager;
    /** @var LoggerChannelFactoryInterface */
    protected $logger;

    public function __construct(
        MailableManager $mailableManager,
        LoggerChannelFactoryInterface $logger
    ) {
        $this->mailableManager = $mailableManager;
        $this->logger = $logger;
    }

    public function to(array $to): MailerInterface
    {
        $this->recepients[MailableInterface::RECEPIENT_TO] = $to;
        return $this;
    }

    public function cc(array $cc): MailerInterface
    {
        $this->recepients[MailableInterface::RECEPIENT_CC] = $cc;
        return $this;
    }

    public function bcc(array $bcc): MailerInterface
    {
        $this->recepients[MailableInterface::RECEPIENT_BCC] = $bcc;
        return $this;
    }

    public function from(string $from): MailerInterface
    {
        $this->from = $from;
        return $this;
    }

    public function replyTo(string $replyTo): MailerInterface
    {
        $this->replyTo = $replyTo;
        return $this;
    }

    /** @return MailableInterface|null */
    protected function prepareMailable(string $id, array $parameters)
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
            return $mailable->build($parameters);
        } catch (DiscardMailException $e) {
            $this->logger->get('wmmailable')->debug(
                sprintf(
                    'Discarded mailable \'%s\'. Reason: %s',
                    $id,
                    empty($e->getMessage()) ? 'none' : $e->getMessage()
                )
            );

            return null;
        }
    }
}
