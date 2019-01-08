<?php

namespace Drupal\wmmailable;

final class WmmailableEvents
{
    /**
     * Will be triggered after the send method is called
     * on the mailable, but before the mail is sent.
     *
     * The event object is an instance of
     * @uses \Drupal\wmmailable\Event\MailableAlterEvent
     */
    const MAILABLE_ALTER = 'wmmailable.mailable.alter';
}
