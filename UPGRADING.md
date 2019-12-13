# Upgrade Guide

This document describes breaking changes and how to upgrade. For a complete list of changes including minor and patch releases, please refer to the [`CHANGELOG`][changelog].

## v2
In this version the interfaces of both `MailableInterface` and
`MailerInterface` changed. Before, when passing recepients to the mailer
service, they were stored in properties on the service. Now, we operate
directly on the `Mailable` object.

For example, this:
```php
$this->mailer
    ->to(['Wieni <info@wieni.be>', 'dieter@wieni.be'])
    ->bcc(['sophie@wieni.be'])
    ->send(
        'contact_form_submission',
        compact('domain', 'firstName', 'lastName', 'email', 'question')
    );
```

becomes this: 
```php
$mail = $this->mailer->create('contact_form_submission')
    ->setRecepients(['Wieni <info@wieni.be>', 'dieter@wieni.be'])
    ->addBcc('sophie@wieni.be')
    ->setParameters(
        compact('domain', 'firstName', 'lastName', 'email', 'question')
    );

$this->mailer->send($mail);
```
