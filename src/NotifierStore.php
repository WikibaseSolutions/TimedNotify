<?php

namespace PegaNotify;

use PegaNotify\Notifiers\ExpiringSoonHubAdminNotifier;
use PegaNotify\Notifiers\ExpiringSoonPageOwnerNotifier;
use PegaNotify\Notifiers\Notifier;

class NotifierStore {
    public const NOTIFIERS = [
        ExpiringSoonHubAdminNotifier::class,
        ExpiringSoonPageOwnerNotifier::class
    ];

    /**
     * @var Notifier[]
     */
    private array $notifierInstancesCache;

    /**
     * Returns instances of notifiers.
     *
     * @return Notifier[]
     */
    public function getNotifiers(): array {
        if ( isset( $this->notifierInstancesCache ) ) {
            return $this->notifierInstancesCache;
        }

        $this->notifierInstancesCache = [];

        foreach ( $this->getNotifierClasses() as $notifierClass ) {
            $this->notifierInstancesCache[] = new $notifierClass();
        }

        return $this->notifierInstancesCache;
    }

    /**
     * Returns the class names of the notifiers.
     *
     * @return string[]|Notifier[]
     */
    public function getNotifierClasses(): array {
        return self::NOTIFIERS;
    }
}