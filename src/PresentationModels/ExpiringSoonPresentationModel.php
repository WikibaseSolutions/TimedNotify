<?php

namespace TimedNotify\PresentationModels;

use EchoEventPresentationModel;

/**
 * Presentation model for the "expiring soon" notification.
 *
 * @see ExpiringSoonNotifier
 */
abstract class ExpiringSoonPresentationModel extends EchoEventPresentationModel {
	/**
	 * @inheritDoc
	 */
	public function canRender(): bool {
		return $this->event->getTitle() && $this->event->getTitle()->exists();
	}

	/**
	 * @inheritDoc
	 */
	public function getIconType(): string {
		return 'timednotify-expiring-soon';
	}

	/**
	 * @inheritDoc
	 */
	public function getPrimaryLink(): array {
		return [
			'url' => $this->event->getTitle()->getFullURL(),
			'label' => $this->msg( 'timednotify-notification-link-text-expiring-soon' )
		];
	}

	/**
	 * @inheritDoc
	 */
	public function getSecondaryLinks(): array {
		return [
			[
				'url' => $this->event->getTitle()->getFullURL(),
				'label' => $this->msg( 'timednotify-notification-link-text-expiring-soon' ),
				'prioritized' => true
			]
		];
	}
}
