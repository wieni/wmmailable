<?php

namespace Drupal\wmmailable\Plugin\Mail;

use Hampe\Inky\Inky;

/**
 * Provides a plugin to format mails
 *
 * @Mail(
 *   id = "mailable_foundation_for_emails",
 *   label = @Translation("Mailable - Foundation for Emails"),
 *   description = @Translation("Mail formatter which transpiles Foundation for Emails markup to regular HTML.")
 * )
 */
class FoundationForEmailsMailableFormatter extends InlineStyleMailableFormatter
{
    /**
     * {@inheritdoc}
     */
    public function format(array $message)
    {
        $message = parent::format($message);

        if (!class_exists('Hampe\Inky\Inky')) {
            throw new \Exception('The hampe/inky package is required to use this formatter.');
        }

        $message['body'] = (new Inky())->releaseTheKraken($message['body']);

        return $message;
    }
}
