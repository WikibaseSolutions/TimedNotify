<?php

/**
 * This file is loaded by MediaWiki\MediaWikiServices::getInstance() during the
 * bootstrapping of the dependency injection framework.
 *
 * @file
 */

use MediaWiki\MediaWikiServices;
use TimedNotify\EchoEventCreator;
use TimedNotify\NotificationRunner;
use TimedNotify\NotifierStore;
use TimedNotify\TimedNotifyServices;
use TimedNotify\PushedNotificationBucket;

return [
    "TimedNotify.EchoEventCreator" => static function (): EchoEventCreator {
        return new EchoEventCreator();
    },
    "TimedNotify.NotificationRunner" => static function ( MediaWikiServices $services ): NotificationRunner {
        return new NotificationRunner(
            TimedNotifyServices::getNotifierStore(),
            TimedNotifyServices::getPushedNotificationBucket(),
            TimedNotifyServices::getEchoEventCreator(),
            $services->getMainConfig()->get( 'TimedNotifyRunRate' )
        );
    },
    "TimedNotify.NotifierStore" => static function ( MediaWikiServices $services ): NotifierStore {
        return new NotifierStore(
            $services->getMainConfig()->get( 'TimedNotifyEnabledNotifiers' )
        );
    },
    "TimedNotify.PushedNotificationBucket" => static function ( MediaWikiServices $services ): PushedNotificationBucket {
        return new PushedNotificationBucket(
            $services->getDBLoadBalancer()->getConnection( DB_PRIMARY ),
            $services->getMainConfig()->get( 'TimedNotifyPushedNotificationRetentionDays' )
        );
    }
];
