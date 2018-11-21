<a href="https://www.wieni.be">
    <img src="https://www.wieni.be/themes/custom/drupack/logo.svg" alt="Wieni logo" title="Wieni" align="right" height="60" />
</a>

wmmailable
======================

[![Latest Stable Version](https://poser.pugx.org/wieni/wmmailable/v/stable)](https://packagist.org/packages/wieni/wmmailable)
[![Total Downloads](https://poser.pugx.org/wieni/wmmailable/downloads)](https://packagist.org/packages/wieni/wmmailable)
[![License](https://poser.pugx.org/wieni/wmmailable/license)](https://packagist.org/packages/wieni/wmmailable)

> A modern, plugin-based API for sending mails in Drupal 8. Inspired by [Laravel](https://laravel.com/docs/5.7/mail)

## Why?
- No 'modern' way to handle mails in Drupal 8: messing with `hook_mail` and `hook_theme` does not really fit in the Wieni Drupal flow with [wmmodel](https://github.com/wieni/wmmodel), [wmcontroller](https://github.com/wieni/wmcontroller), etc.
- No clean, object-oriented API, e.g. to add CC / BCC-adresses you have to manually add headers
- Not intuitive, logic is scattered across multiple files, tends to get messy

## How does it work?

### Building mails
- Mails are annotated plugins
- Each class represents one mail
- Dependency injection is possible by implementing the `ContainerFactoryPluginInterface` ([tutorial](https://chromatichq.com/blog/dependency-injection-drupal-8-plugins))

```php
<?php

namespace Drupal\wmcustom\Mail;

/**
 * @Mailable(
 *     id = "contact_form_submission"
 * )
 */
class ContactFormSubmission extends MailableBase
{
    public function build(array $parameters): MailableInterface;
}

```
### Sending mails
- Mails are sent through the `Mailer` service
- Two standard `Mailer` implementations are provided: `mailable.mailer.direct` and `mailable.mailer.queued`
- The active implementation can be changed in the `mailable.settings.yml` config

```php
<?php

namespace Drupal\wmcustom\Form;

class ContactForm extends FormBase
{
    /** @var MailerInterface */
    protected $mailer;

    public function __construct(
        MailerInterface $mailer
    ) {
        $this->mailer = $mailer;
    }

    public function submitForm(array &$form, FormStateInterface $formState)
    {
        $this->mailer
            ->to(['Wieni <info@wieni.be>', 'dieter@wieni.be'])
            ->bcc(['sophie@wieni.be'])
            ->send(
                'contact_form_submission',
                compact('domain', 'firstName', 'lastName', 'email', 'question')
            );
	}

	public static function create(ContainerInterface $container)
	{
	    return new static(
	        $container->get('mailable.mailer')
        );
    }
}
```

- The mail template should be placed in `<active theme>/mail/<mailable id with dashes instead of underscores>`

### Hooks
- One hook is provided, `hook_mailable_alter`. This hook is called after the `send` method is called on the Mailable, but before the mail is sent.

```php
<?php

function wmcustom_mailable_alter(MailableInterface $mail)
{
    $mail->setHeader('X-SES-SOURCE-ARN', '<...>');
}
```