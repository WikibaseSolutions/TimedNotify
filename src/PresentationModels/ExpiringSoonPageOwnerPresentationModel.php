<?php

namespace PegaNotify\PresentationModels;

use Message;
use PegaNotify\Notifiers\ExpiringSoonNotifier;

class ExpiringSoonPageOwnerPresentationModel extends ExpiringSoonPresentationModel {
    /**
     * @inheritDoc
     */
    public function getHeaderMessage(): Message {
        return $this->msg( 'peganotify-notification-header-expiring-soon-page-owner' )
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
        return $this->msg( 'peganotify-notification-subject-expiring-soon-page-owner' )
            ->params(
                $this->event->getTitle()->getFullText(),
                $this->msg( 'peganotify-expiring-soon-remaining-days' )->numParams(
                    $this->event->getExtra()[ExpiringSoonNotifier::TIME_REMAINING_EXTRA_KEY]
                )
            );
    }
}