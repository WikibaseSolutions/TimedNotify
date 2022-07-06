<?php

namespace PegaNotify\Notifiers;

use EchoEvent;
use Title;
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
     * Returns for which pages notifications should be sent. The return value should be an array of pages, where the
     * key can either be numeric or a string. If the key is numeric, the notification will be emitted unconditionally.
     * If the key is a string, the notification will only be emitted if a notification with the same key (scoped to each
     * notifier) has not already been emitted.
     *
     * @return Title[]
     */
    public function getPages(): array;

    /**
     * Returns the notification data for the given page used in the event that will be emitted by Echo.
     *
     * @param Title $title The Title to get the notification data for
     * @return array The notification data
     */
    public function getNotificationData( Title $title ): array;

    /**
     * Returns the users that should be notified by the given event.
     *
     * @param EchoEvent $event The event to get the users for
     * @return User[] The user(s) to notify
     */
    public function getNotificationUsers( EchoEvent $event ): array;
}