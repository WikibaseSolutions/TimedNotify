<?php

namespace TimedNotify\Notifiers;

use EchoEvent;
use TimedNotify\PresentationModels\ExpiringSoonPageOwnerPresentationModel;

class ExpiringSoonPageOwnerNotifier extends ExpiringSoonNotifier {
	public const NOTIFICATION_NAME = "TimedNotifyExpiringSoonPageOwner";

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
