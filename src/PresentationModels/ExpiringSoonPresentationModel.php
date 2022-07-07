<?php

namespace PegaNotify\PresentationModels;

use EchoEventPresentationModel;
use Message;
use PegaNotify\Notifiers\ExpiringSoonNotifier;

/**
 * Presentation model for the "expiring soon" notification.
 *
 * @see ExpiringSoonNotifier
 */
class ExpiringSoonPresentationModel extends EchoEventPresentationModel {
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
        return 'peganotify-expiring-soon';
    }

    /**
     * @inheritDoc
     */
    public function getHeaderMessage(): Message {
        return $this->msg( 'peganotify-notification-header-expiring-soon' )
            ->params(
                $this->event->getTitle()->getFullText(),
                $this->msg( 'peganotify-expiring-soon-remaining-days' )->numParams(
                    $this->event->getExtra()[ExpiringSoonNotifier::TIME_REMAINING_EXTRA_KEY]
                )
            );
    }

    /**
     * @inheritDoc
     */
    public function getSubjectMessage(): Message {
        return $this->msg( 'peganotify-notification-subject-expiring-soon' )
            ->params(
                $this->event->getTitle()->getFullText(),
                $this->msg( 'peganotify-expiring-soon-remaining-days' )->numParams(
                    $this->event->getExtra()[ExpiringSoonNotifier::TIME_REMAINING_EXTRA_KEY]
                )
            );
    }

    /**
     * @inheritDoc
     */
    public function getPrimaryLink(): array {
        return [
            'url' => $this->event->getTitle()->getFullURL(),
            'label' => $this->msg( 'peganotify-notification-link-text-expiring-soon' )
        ];
    }

    /**
     * @inheritDoc
     */
    public function getSecondaryLinks(): array {
        return [
            [
                'url' => $this->event->getTitle()->getFullURL(),
                'label' => $this->msg( 'peganotify-notification-link-text-expiring-soon' ),
                'prioritized' => true
            ]
        ];
    }
}