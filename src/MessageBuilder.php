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

    public function populateMessage(array &$message, MailableInterface $mailable): void
    {
        $this->setLangcode($message, $mailable);
        $this->setBody($message, $mailable);
        $this->setSubject($message, $mailable);
        $this->setFrom($message, $mailable);
        $this->setReplyTo($message, $mailable);
        $this->setRecepients($message, $mailable);
        $this->setContentType($message, $mailable);
        $this->setHeaders($message, $mailable);
    }

    protected function setBody(array &$message, MailableInterface $mailable): void
    {
        $themeHook = sprintf('wmmailable.%s.%s', $mailable->getKey(), $mailable->getLangcode());
        $themeHook = str_replace('\\', '_', $themeHook);

        $render = [
            '#theme' => $themeHook,
            '#_data' => $mailable->getParameters(),
        ];

        $message['body'] = [$this->renderer->renderPlain($render)];
    }

    protected function setSubject(array &$message, MailableInterface $mailable): void
    {
        if ($subject = $mailable->getSubject()) {
            $message['subject'] = $subject;
        }
    }

    protected function setFrom(array &$message, MailableInterface $mailable): void
    {
        if ($from = $mailable->getFrom()) {
            $message['from'] = $from;
        }
    }

    protected function setReplyTo(array &$message, MailableInterface $mailable): void
    {
        if ($replyTo = $mailable->getReplyTo()) {
            $message['reply-to'] = $replyTo;
        }
    }

    protected function setLangcode(array &$message, MailableInterface $mailable): void
    {
        if (!$mailable->getLangcode()) {
            $mailable->setLangcode(
                $this->languageManager->getDefaultLanguage()->getId()
            );
        }

        $message['langcode'] = $mailable->getLangcode();
    }

    protected function setRecepients(array &$message, MailableInterface $mailable): void
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

    protected function setContentType(array &$message, MailableInterface $mailable): void
    {
        $contentType = $mailable->getContentType()
            ?? $this->config->get('mailable.defaults')->get('contentType')
            ?? 'text/html';

        $charset = $mailable->getCharset()
            ?? $this->config->get('mailable.defaults')->get('charset')
            ?? 'utf-8';

        $message['headers']['Content-Type'] = "{$contentType}; charset={$charset}";
    }

    protected function setHeaders(array &$message, MailableInterface $mailable): void
    {
        foreach ($mailable->getHeaders() as $name => $header) {
            $message['headers'][$name] = $header;
        }
    }
}
