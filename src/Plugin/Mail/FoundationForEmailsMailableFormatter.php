<?php

namespace Drupal\wmmailable\Plugin\Mail;

use Hampe\Inky\Inky;

/**
 * Provides a plugin to format mails
 *
 * @Mail(
 *     id = "mailable_foundation_for_emails",
 *     label = @Translation("Mailable - Foundation for Emails"),
 *     description = @Translation("Mail formatter which transpiles Foundation for Emails markup to regular HTML.")
 * )
 */
class FoundationForEmailsMailableFormatter extends MailableFormatterBase
{
    public function format(array $message)
    {
        if (!class_exists('Hampe\Inky\Inky')) {
            throw new \Exception('The hampe/inky package is required to use this formatter.');
        }

        /** @var PlainMailableFormatter $plainFormatter */
        $plainFormatter = $this->mailManager->createInstance('mailable_plain');
        /** @var InlineStyleMailableFormatter $inlineStyle */
        $inlineStyleFormatter = $this->mailManager->createInstance('mailable_inline_style');

        $message = $plainFormatter->format($message);
        $message['body'] = (new Inky())->releaseTheKraken($message['body']);
        $message = $inlineStyleFormatter->format($message);

        return $message;
    }
}
