<?php

namespace Drupal\wmmailable;

use Drupal\Core\Plugin\PluginBase;

abstract class MailableBase extends PluginBase implements BuildableMailableInterface
{
    /** @var string */
    protected $langcode = '';

    /** @var string */
    protected $subject = '';
    /** @var string */
    protected $from = '';
    /** @var string */
    protected $replyTo = '';
    /** @var array */
    protected $recepients = [];
    /** @var array */
    protected $parameters = [];
    /** @var array */
    protected $libraries = [];
    /** @var array */
    protected $headers = [];
    /** @var string */
    protected $contentType;
    /** @var string */
    protected $charset;

    public function build(): MailableInterface
    {
        return $this;
    }

    public function getKey(): string
    {
        return $this->pluginDefinition['id'];
    }

    public function getModule(): string
    {
        return $this->pluginDefinition['module'];
    }

    public function getTemplate(): string
    {
        return $this->pluginDefinition['template'] ?? '';
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): MailableInterface
    {
        $this->subject = $subject;
        return $this;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function setFrom(string $from): MailableInterface
    {
        $this->from = $from;
        return $this;
    }

    public function getReplyTo(): string
    {
        return $this->replyTo;
    }

    public function setReplyTo(string $replyTo): MailableInterface
    {
        $this->replyTo = $replyTo;
        return $this;
    }

    public function getLangcode(): string
    {
        return $this->langcode;
    }

    public function setLangcode(string $langcode): MailableInterface
    {
        $this->langcode = $langcode;
        return $this;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function setParameters(array $parameters): MailableInterface
    {
        $this->parameters = $parameters;
        return $this;
    }

    public function getLibraries(): array
    {
        return $this->libraries;
    }

    public function setLibraries(array $libraries): MailableInterface
    {
        $this->libraries = $libraries;
        return $this;
    }

    public function addLibrary(string $library): MailableInterface
    {
        if (!in_array($library, $this->libraries)) {
            $this->libraries[] = $library;
        }
        return $this;
    }

    public function getRecepients(string $type = self::RECEPIENT_TO): array
    {
        return $this->recepients[$type] ?? [];
    }

    public function setRecepients(array $recepients, string $type = self::RECEPIENT_TO): MailableInterface
    {
        $this->recepients[$type] = $recepients;
        return $this;
    }

    public function addRecepient(string $recepient, string $type = self::RECEPIENT_TO): MailableInterface
    {
        if (!in_array($recepient, $this->recepients[$type] ?? [])) {
            $this->recepients[$type][] = $recepient;
        }
        return $this;
    }

    public function getCc(): array
    {
        return $this->getRecepients(self::RECEPIENT_CC);
    }

    public function setCc(array $recepients): MailableInterface
    {
        return $this->setRecepients($recepients, self::RECEPIENT_CC);
    }

    public function addCc(string $recepient): MailableInterface
    {
        return $this->addRecepient($recepient, self::RECEPIENT_CC);
    }

    public function getBcc(): array
    {
        return $this->getRecepients(self::RECEPIENT_BCC);
    }

    public function setBcc(array $recepients): MailableInterface
    {
        return $this->setRecepients($recepients, self::RECEPIENT_BCC);
    }

    public function addBcc(string $recepient): MailableInterface
    {
        return $this->addRecepient($recepient, self::RECEPIENT_BCC);
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getHeader(string $name): string
    {
        return $this->headers[$name] ?? '';
    }

    public function setHeader(string $name, string $header): MailableInterface
    {
        $this->headers[$name] = $header;
        return $this;
    }

    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    public function setContentType(string $contentType): MailableInterface
    {
        $this->contentType = $contentType;
        return $this;
    }

    public function getCharset(): ?string
    {
        return $this->charset;
    }

    public function setCharset(string $charset): MailableInterface
    {
        $this->charset = $charset;
        return $this;
    }
}
