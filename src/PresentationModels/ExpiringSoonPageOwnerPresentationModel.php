<?php

namespace TimedNotify\PresentationModels;

use Message;
use TimedNotify\Notifiers\ExpiringSoonNotifier;

class ExpiringSoonPageOwnerPresentationModel extends ExpiringSoonPresentationModel {
    /**
     * @inheritDoc
     */
    public function getHeaderMessage(): Message {
        return $this->msg( 'timednotify-notification-header-expiring-soon-page-owner' )
            ->params(
                $this->event->getTitle()->getFullText(),
                $this->msg( 'timednotify-expiring-soon-remaining-days' )->numParams(
                    $this->event->getExtra()[ExpiringSoonNotifier::TIME_REMAINING_EXTRA_KEY]
                )
            );
    }

    /**
     * @inheritDoc
     */
    public function getSubjectMessage(): Message {
        return $this->msg( 'timednotify-notification-subject-expiring-soon-page-owner' )
            ->params(
                $this->event->getTitle()->getFullText(),
                $this->msg( 'timednotify-expiring-soon-remaining-days' )->numParams(
                    $this->event->getExtra()[ExpiringSoonNotifier::TIME_REMAINING_EXTRA_KEY]
                )
            );
    }
}