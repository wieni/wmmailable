<?php

namespace Drupal\wmmailable;

interface MailableInterface
{
    const RECEPIENT_TO = 'RECEPIENT_TO';
    const RECEPIENT_CC = 'RECEPIENT_CC';
    const RECEPIENT_BCC = 'RECEPIENT_BCC';

    public function build(array $parameters): MailableInterface;

    public function getKey(): string;

    public function getSubject(): string;

    public function setSubject(string $subject): MailableInterface;

    public function getTemplate(): string;

    public function setTemplate(string $template): MailableInterface;

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
}