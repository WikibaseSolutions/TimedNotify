<?php

namespace PegaNotify\Notifiers;

use EchoEvent;
use PegaNotify\PresentationModels\ExpiringSoonPresentationModel;
use Title;

/**
 * Implements the "expiring soon" notifier. This notifier is triggered when a page's verified status is about to
 * expire soon. Notifications are sent on the following schedule:
 *
 * - 2 weeks before expiration
 * - 1 week before expiration
 * - 1 day before expiration
 *
 * @see https://pegadigitalit.atlassian.net/browse/KHUB-981
 */
class ExpiringSoonNotifier implements Notifier {
    public const NOTIFICATION_NAME = "PegaNotifyExpiringSoon";

    /**
     * @inheritDoc
     */
    public static function getName(): string {
        return static::NOTIFICATION_NAME;
    }

    /**
     * @inheritDoc
     */
    public static function getPresentationModel(): string {
        return ExpiringSoonPresentationModel::class;
    }

    /**
     * @inheritDoc
     */
    public function getPages(): array {
        return [
            Title::newFromText( 'Main Page' )
        ];
    }

    /**
     * @inheritDoc
     */
    public function getNotificationData( Title $title ): array {
        return ['title' => $title];
    }

    /**
     * @inheritDoc
     */
    public function getNotificationUsers(EchoEvent $event ): array {
        return [
            \User::newFromName( 'Admin' )
        ];
    }
}