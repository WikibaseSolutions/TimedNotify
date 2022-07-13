<?php

namespace TimedNotify\PresentationModels;

use MediaWiki\MediaWikiServices;
use Message;
use TimedNotify\Notifiers\ExpiringSoonNotifier;

class ExpiringSoonHubAdminPresentationModel extends ExpiringSoonPresentationModel {
	/**
	 * @inheritDoc
	 */
	public function getHeaderMessage(): Message {
		$hubName = MediaWikiServices::getInstance()->getNamespaceInfo()->getCanonicalName(
			$this->event->getTitle()->getNamespace()
		);

		return $this->msg( 'timednotify-notification-header-expiring-soon-hub-admin' )
			->params(
				$this->event->getTitle()->getFullText(),
				$this->msg( 'timednotify-expiring-soon-remaining-days' )->numParams(
					$this->event->getExtra()[ExpiringSoonNotifier::TIME_REMAINING_EXTRA_KEY]
				),
				$hubName
			);
	}

	/**
	 * @inheritDoc
	 */
	public function getSubjectMessage(): Message {
		$hubName = MediaWikiServices::getInstance()->getNamespaceInfo()->getCanonicalName(
			$this->event->getTitle()->getNamespace()
		);

		if ( empty( $hubName ) ) {
			$hubName = 'main';
		}

		return $this->msg( 'timednotify-notification-subject-expiring-soon-hub-admin' )
			->params(
				$this->event->getTitle()->getFullText(),
				$this->msg( 'timednotify-expiring-soon-remaining-days' )->numParams(
					$this->event->getExtra()[ExpiringSoonNotifier::TIME_REMAINING_EXTRA_KEY]
				),
				$hubName
			);
	}
}
