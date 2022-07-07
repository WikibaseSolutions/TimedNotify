<?php

namespace PegaNotify\MediaWiki\Hooks;

use EnConverter;
use MediaWiki\Hook\BeforeInitializeHook;
use MediaWiki\MediaWikiServices;
use MWException;
use PegaNotify\NotificationRunner;
use Title;

/**
 * This class is responsible for running the notifications during the initialisation of the wiki.
 */
class BeforeInitializeNotificationRunner implements BeforeInitializeHook {
    /**
     * @var NotificationRunner The notification runner to use
     */
    private NotificationRunner $notificationRunner;

    /**
     * @param NotificationRunner $notificationRunner The notification runner to use
     */
    public function __construct( NotificationRunner $notificationRunner ) {
        $this->notificationRunner = $notificationRunner;
    }

    /**
     * @inheritDoc
     * @throws MWException
     */
    public function onBeforeInitialize( $title, $unused, $output, $user, $request, $mediaWiki ): void {
        $converter = new EnConverter(\MediaWiki\MediaWikiServices::getInstance()->getContentLanguage());

        $title = Title::newFromText('Eigenschap:Verified');
        $enTitle = \MWNamespace::getCanonicalName( $title->getNamespace() ) . ':' . $title->getText();
        var_dump($enTitle);

        $this->notificationRunner->runOpportunistic(
            MediaWikiServices::getInstance()->getMainConfig()->get( 'PegaNotifyRunDeferred' )
        );
    }
}