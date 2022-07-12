<?php

namespace TimedNotify;

use TimedNotify\Notifiers\ExpiringSoonHubAdminNotifier;
use TimedNotify\Notifiers\ExpiringSoonPageOwnerNotifier;
use TimedNotify\Notifiers\Notifier;

class NotifierStore {
    private const NOTIFIERS = [
        ExpiringSoonHubAdminNotifier::class,
        ExpiringSoonPageOwnerNotifier::class
    ];

    /**
     * @var string[] Array of notifiers names that are enabled
     */
    private array $enabledNotifiers;

    /**
     * @var Notifier[] Cache of instantiated notifiers
     */
    private array $notifierInstancesCache;

    /**
     * @param bool[] $enabledNotifiers Array of notifiers that are enabled
     */
    public function __construct( array $enabledNotifiers ) {
        $this->enabledNotifiers = $enabledNotifiers;
    }

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

        foreach ( self::NOTIFIERS as $notifierClass ) {
            $instance = new $notifierClass();

            if ( isset( $this->enabledNotifiers[$instance->getName()] ) && $this->enabledNotifiers[$instance->getName()] === true ) {
                $this->notifierInstancesCache[] = $instance;
            }
        }

        return $this->notifierInstancesCache;
    }
}