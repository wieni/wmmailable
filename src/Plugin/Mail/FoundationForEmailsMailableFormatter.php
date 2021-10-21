<?php

namespace Drupal\wmmailable\Plugin\Mail;

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
        /** @var PlainMailableFormatter $plainFormatter */
        $plainFormatter = $this->mailManager->createInstance('mailable_plain');
        /** @var InlineStyleMailableFormatter $inlineStyle */
        $inlineStyleFormatter = $this->mailManager->createInstance('mailable_inline_style');

        $message = $plainFormatter->format($message);
        $message['body'] = $this->transpile($message['body']);
        $message = $inlineStyleFormatter->format($message);

        return $message;
    }

    protected function transpile(string $body): string
    {
        if (class_exists('Hampe\Inky\Inky')) {
            return (new \Hampe\Inky\Inky())->releaseTheKraken($body);
        }

        if (function_exists('\Pinky\transformString')) {
            return \Pinky\transformString('<row>Contents</row>')->saveHTML();
        }

        throw new \Exception('The hampe/inky or lorenzo/pinky package is required to use this formatter.');
    }
}
