<?php

namespace PegaNotify;

use PegaNotify\Notifiers\ExpiringSoonNotifier;
use PegaNotify\Notifiers\Notifier;

class NotifierStore {
    public const NOTIFIERS = [
        ExpiringSoonNotifier::class
    ];

    /**
     * @var Notifier[] The instantiated notifiers
     */
    private array $instances;

    /**
     * @return array
     */
    public function getNotifiers(): array {
        if ( !isset( $this->instances ) ) {
            $this->instances = $this->constructNotifiers();
        }

        return $this->instances;
    }

    /**
     * Eagerly constructs all notifiers and returns them.
     *
     * @return Notifier[] The constructed notifiers
     */
    private function constructNotifiers(): array {
        $notifiers = [];

        foreach ( static::NOTIFIERS as $notifier ) {
            $notifiers[] = new $notifier();
        }

        return $notifiers;
    }
}