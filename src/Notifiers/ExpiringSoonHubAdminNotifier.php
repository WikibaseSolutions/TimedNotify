<?php

namespace TimedNotify\Notifiers;

use EchoEvent;
use ExtensionRegistry;
use TimedNotify\PresentationModels\ExpiringSoonHubAdminPresentationModel;
use User;
use WSS\NamespaceRepository;

class ExpiringSoonHubAdminNotifier extends ExpiringSoonNotifier {
    public const NOTIFICATION_NAME = "TimedNotifyExpiringSoonHubAdmin";

    /**
     * @inheritDoc
     */
    public function getName(): string {
        return self::NOTIFICATION_NAME;
    }

    /**
     * @inheritDoc
     */
    public function getPresentationModel(): string {
        return ExpiringSoonHubAdminPresentationModel::class;
    }

    /**
     * @inheritDoc
     */
    public static function getNotificationUsers( EchoEvent $event ): array {
        if ( !ExtensionRegistry::getInstance()->isLoaded( 'WSSpaces' ) ) {
            // Make sure WSSpaces is installed and enabled
            return [];
        }

        if ( $event->getTitle() === null ) {
            return [];
        }

        $adminIDs = NamespaceRepository::getNamespaceAdmins( $event->getTitle()->getNamespace() );
        $adminUsers = array_map( fn ( int $id ) => User::newFromId( $id ), $adminIDs );

        // Filter any admins that do not exist (are NULL)
        return array_filter( $adminUsers );
    }

    /**
     * Filter any users that are page owner of the page to prevent duplicate notifications.
     *
     * @inheritDoc
     */
    public static function getFilteredUsers( EchoEvent $event ): array {
        if ( $event->getTitle() === null ) {
            return [];
        }

        // Do NOT send a notification to any hub admins that are also owner of the page (to prevent duplicate
        // notifications).
        return self::getPageOwners( $event->getTitle() );
    }
}