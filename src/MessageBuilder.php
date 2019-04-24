<?php

namespace Drupal\wmmailable;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Render\RendererInterface;

class MessageBuilder
{
    /** @var ConfigFactoryInterface */
    protected $config;
    /** @var LanguageManagerInterface */
    protected $languageManager;
    /** @var RendererInterface */
    protected $renderer;

    public function __construct(
        ConfigFactoryInterface $configFactory,
        LanguageManagerInterface $languageManager,
        RendererInterface $renderer
    ) {
        $this->config = $configFactory;
        $this->languageManager = $languageManager;
        $this->renderer = $renderer;
    }

    public function populateMessage(array &$message, MailableInterface $mailable)
    {
        $this->setBody($message, $mailable);
        $this->setSubject($message, $mailable);
        $this->setFrom($message, $mailable);
        $this->setReplyTo($message, $mailable);
        $this->setLangcode($message, $mailable);
        $this->setRecepients($message, $mailable);
        $this->setContentType($message, $mailable);
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

    protected function setReplyTo(array &$message, MailableInterface $mailable)
    {
        if ($replyTo = $mailable->getReplyTo()) {
            $message['reply-to'] = $replyTo;
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

    protected function setContentType(array &$message, MailableInterface $mailable)
    {
        $contentType = $mailable->getContentType()
            ?? $this->config->get('mailable.defaults')->get('contentType')
            ?? 'text/html';

        $charset = $mailable->getCharset()
            ?? $this->config->get('mailable.defaults')->get('charset')
            ?? 'utf-8';

        $message['headers']['Content-Type'] = "{$contentType}; charset={$charset}";
    }

    protected function setHeaders(array &$message, MailableInterface $mailable)
    {
        foreach ($mailable->getHeaders() as $name => $header) {
            $message['headers'][$name] = $header;
        }
    }
}
