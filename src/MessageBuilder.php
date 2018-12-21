<?php

namespace Drupal\wmmailable;

use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Render\RendererInterface;

class MessageBuilder
{
    /** @var LanguageManagerInterface */
    protected $languageManager;
    /** @var RendererInterface */
    protected $renderer;

    public function __construct(
        LanguageManagerInterface $languageManager,
        RendererInterface $renderer
    ) {
        $this->languageManager = $languageManager;
        $this->renderer = $renderer;
    }

    public function populateMessage(array &$message, MailableInterface $mailable)
    {
        $this->setBody($message, $mailable);
        $this->setSubject($message, $mailable);
        $this->setFrom($message, $mailable);
        $this->setLangcode($message, $mailable);
        $this->setRecepients($message, $mailable);
        $this->setHeaders($message, $mailable);
    }

    protected function setBody(array &$message, MailableInterface $mailable)
    {
        $render = [
            '#theme' => "wmmailable.{$mailable->getKey()}",
            '#_data' => $mailable->getParameters(),
        ];

        $message['body'] = [$this->renderer->renderPlain($render)];
    }

    protected function setSubject(array &$message, MailableInterface $mailable)
    {
        if ($subject = $mailable->getSubject()) {
            $message['subject'] = $subject;
        }
    }

    protected function setFrom(array &$message, MailableInterface $mailable)
    {
        if ($from = $mailable->getFrom()) {
            $message['from'] = $from;
        }
    }

    protected function setLangcode(array &$message, MailableInterface $mailable)
    {
        if ($langcode = $mailable->getLangcode()) {
            $message['langcode'] = $langcode;
        }

        if (!isset($message['langcode'])) {
            $message['langcode'] = $this->languageManager->getDefaultLanguage()->getId();
        }
    }

    protected function setRecepients(array &$message, MailableInterface $mailable)
    {
        $to = $mailable->getRecepients(MailableInterface::RECEPIENT_TO);
        $cc = $mailable->getRecepients(MailableInterface::RECEPIENT_CC);
        $bcc = $mailable->getRecepients(MailableInterface::RECEPIENT_BCC);

        if (!empty($to)) {
            $message['to'] = implode(', ', $to);
        }

        if (!empty($cc)) {
            $message['headers']['Cc'] = implode(', ', $cc);
        }

        if (!empty($bcc)) {
            $message['headers']['Bcc'] = implode(', ', $bcc);
        }
    }

    protected function setHeaders(array &$message, MailableInterface $mailable)
    {
        foreach ($mailable->getHeaders() as $name => $header) {
            $message['headers'][$name] = $header;
        }
    }
}
