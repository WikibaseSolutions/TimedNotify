<?php

namespace PegaNotify\Notifiers;

use EchoEvent;
use User;

/**
 * This interface is implemented by all periodic page notifiers.
 */
interface Notifier {
    /**
     * Returns the name of this notification.
     *
     * @return string
     */
    public static function getName(): string;

    /**
     * Returns the class name of the presentation model.
     *
     * @return string
     */
    public static function getPresentationModel(): string;

    /**
     * Returns additional icons to define.
     *
     * @return array
     */
    public static function getIcons(): array;

    /**
     * Returns an array of notifications that should be sent. A notification should have the following form:
     *
     * [
     *     'id'    => (string) a unique identifier for this notification (will automatically be scoped to the notifier).
     *                         The notification will only be emitted if a notification with this key has not already
     *                         been emitted. If this value is omitted, the notification will be emitted
     *                         unconditionally. (optional),
     *     'data'  => (array)  additional data to add to the notification. (optional)
     * ]
     *
     * @return array[]
     */
    public static function getNotifications(): array;

    /**
     * Returns the users that should be notified by the given event.
     *
     * @param EchoEvent $event The event to get the users for
     * @return User[] The user(s) to notify
     */
    public static function getNotificationUsers( EchoEvent $event ): array;
}