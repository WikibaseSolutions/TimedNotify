<?php

namespace PegaNotify;

use PegaNotify\Notifiers\ExpiringSoonNotifier;
use PegaNotify\Notifiers\Notifier;

class NotifierStore {
    public const NOTIFIERS = [
        ExpiringSoonNotifier::class
    ];

    /**
     * Returns the class names of the notifiers.
     *
     * @return string[]|Notifier[]
     */
    public function getNotifierClasses(): array {
        return static::NOTIFIERS;
    }
}