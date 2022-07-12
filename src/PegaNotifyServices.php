<?php

namespace PegaNotify;

use MediaWiki\MediaWikiServices;
use Wikimedia\Services\ServiceContainer;

/**
 * Getter for all PegaNotify services. This class reduces the risk of mistyping
 * a service name and serves as the interface for retrieving services for PegaNotify.
 *
 * @note Program logic should use dependency injection instead of this class whenever
 * possible.
 *
 * @note This class should only contain static methods.
 */
final class PegaNotifyServices {
    /**
     * Disable the construction of this class by making the constructor private.
     */
    private function __construct() {
    }

    public static function getEchoEventCreator( ?ServiceContainer $services = null ): EchoEventCreator {
        return self::getService( "EchoEventCreator", $services );
    }

    public static function getNotificationRunner( ?ServiceContainer $services = null ): NotificationRunner {
        return self::getService( "NotificationRunner", $services );
    }

    public static function getNotifierStore( ?ServiceContainer $services = null ): NotifierStore {
        return self::getService( "NotifierStore", $services );
    }

    public static function getPushedNotificationBucket( ?ServiceContainer $services = null ): PushedNotificationBucket {
        return self::getService( "PushedNotificationBucket", $services );
    }

    private static function getService( string $service, ?ServiceContainer $services ) {
        return ( $services ?: MediaWikiServices::getInstance() )->getService( "PegaNotify.$service" );
    }
}