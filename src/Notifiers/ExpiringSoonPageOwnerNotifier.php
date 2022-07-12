<?php

namespace PegaNotify\Notifiers;

use EchoEvent;
use PegaNotify\PresentationModels\ExpiringSoonPageOwnerPresentationModel;

class ExpiringSoonPageOwnerNotifier extends ExpiringSoonNotifier {
    public const NOTIFICATION_NAME = "PegaNotifyExpiringSoonPageOwner";

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
        return ExpiringSoonPageOwnerPresentationModel::class;
    }

    /**
     * @inheritDoc
     */
    public static function getNotificationUsers( EchoEvent $event ): array {
        if ( $event->getTitle() === null ) {
            return [];
        }

        return self::getPageOwners( $event->getTitle() );
    }
}