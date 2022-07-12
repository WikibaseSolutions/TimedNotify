<?php

namespace PegaNotify\PresentationModels;

use MediaWiki\MediaWikiServices;
use Message;
use PegaNotify\Notifiers\ExpiringSoonNotifier;
use Title;

class ExpiringSoonHubAdminPresentationModel extends ExpiringSoonPresentationModel {
    /**
     * @inheritDoc
     */
    public function getHeaderMessage(): Message {
        $hubName = MediaWikiServices::getInstance()->getNamespaceInfo()->getCanonicalName(
            $this->event->getTitle()->getNamespace()
        );



        return $this->msg( 'peganotify-notification-header-expiring-soon-hub-admin' )
            ->params(
                $this->event->getTitle()->getFullText(),
                $this->msg( 'peganotify-expiring-soon-remaining-days' )->numParams(
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

        return $this->msg( 'peganotify-notification-subject-expiring-soon-hub-admin' )
            ->params(
                $this->event->getTitle()->getFullText(),
                $this->msg( 'peganotify-expiring-soon-remaining-days' )->numParams(
                    $this->event->getExtra()[ExpiringSoonNotifier::TIME_REMAINING_EXTRA_KEY]
                ),
                $hubName
            );
    }
}