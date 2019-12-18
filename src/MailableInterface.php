<?php

namespace Drupal\wmmailable;

use Drupal\wmmailable\Exception\DiscardMailException;

interface MailableInterface
{
    public const RECEPIENT_TO = 'RECEPIENT_TO';
    public const RECEPIENT_CC = 'RECEPIENT_CC';
    public const RECEPIENT_BCC = 'RECEPIENT_BCC';

    /** @throws DiscardMailException if the mail should be discarded */
    public function build(): MailableInterface;

    public function getKey(): string;

    public function getModule(): string;

    public function getTemplate(): string;

    public function getSubject(): string;

    public function setSubject(string $subject): MailableInterface;

    public function getFrom(): string;

    public function setFrom(string $from): MailableInterface;

    public function getReplyTo(): string;

    public function setReplyTo(string $replyTo): MailableInterface;

    public function getLangcode(): string;

    public function setLangcode(string $langcode): MailableInterface;

    public function getParameters(): array;

    public function setParameters(array $parameters): MailableInterface;

    public function getLibraries(): array;

    public function setLibraries(array $libraries): MailableInterface;

    public function addLibrary(string $library): MailableInterface;

    public function getRecepients(string $type = self::RECEPIENT_TO): array;

    public function setRecepients(array $recepients, string $type = self::RECEPIENT_TO): MailableInterface;

    public function addRecepient(string $recepient, string $type = self::RECEPIENT_TO): MailableInterface;

    public function getCc(): array;

    public function setCc(array $recepients): MailableInterface;

    public function addCc(string $recepient): MailableInterface;

    public function getBcc(): array;

    public function setBcc(array $recepients): MailableInterface;

    public function addBcc(string $recepient): MailableInterface;

    public function getHeaders(): array;

    public function getHeader(string $name): string;

    public function setHeader(string $name, string $header): MailableInterface;

    public function getContentType(): ?string;

    public function setContentType(string $contentType): MailableInterface;

    public function getCharset(): ?string;

    public function setCharset(string $charset): MailableInterface;
}
