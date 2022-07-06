<?php

/**
 * This file is loaded by MediaWiki\MediaWikiServices::getInstance() during the
 * bootstrapping of the dependency injection framework.
 *
 * @file
 */

use MediaWiki\MediaWikiServices;
use PegaNotify\NotificationRunner;
use PegaNotify\NotifierStore;
use PegaNotify\PegaNotifyServices;
use PegaNotify\PushedNotificationBucket;

return [
    "PegaNotify.NotificationRunner" => static function ( MediaWikiServices $services ): NotificationRunner {
        return new NotificationRunner(
            PegaNotifyServices::getNotifierStore(),
            PegaNotifyServices::getPushedNotificationBucket(),
            $services->getMainConfig()->get( 'PegaNotifyRunRate' )
        );
    },
    "PegaNotify.NotifierStore" => static function (): NotifierStore {
        return new NotifierStore();
    },
    "PegaNotify.PushedNotificationBucket" => static function ( MediaWikiServices $services ): PushedNotificationBucket {
        return new PushedNotificationBucket(
            $services->getDBLoadBalancer()->getConnection( DB_PRIMARY ),
            $services->getMainConfig()->get( 'PegaNotifyPushedNotificationRetentionDays' )
        );
    }
];
