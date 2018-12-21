<?php

namespace Drupal\wmmailable;

use Drupal\Core\Plugin\PluginBase;

abstract class MailableBase extends PluginBase implements MailableInterface
{
    /** @var string */
    protected $langcode = '';

    /** @var string */
    protected $subject = '';
    /** @var string */
    protected $from = '';
    /** @var array */
    protected $recepients = [];
    /** @var array */
    protected $parameters = [];
    /** @var array */
    protected $libraries = [];
    /** @var array */
    protected $headers = [];

    public function build(array $parameters): MailableInterface
    {
        return $this->setParameters($parameters);
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
        if (array_search($library, $this->libraries) === false) {
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
        if (array_search($recepient, $this->recepients[$type] ?? []) === false) {
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
}
