<?php

namespace PegaNotify\Tests\Integration;

use MediaWikiIntegrationTestCase;
use PegaNotify\EchoEventCreator;
use PegaNotify\NotificationRunner;
use PegaNotify\NotifierStore;
use PegaNotify\PegaNotifyServices;
use PegaNotify\PushedNotificationBucket;

/**
 * @covers \PegaNotify\PegaNotifyServices
 */
class PegaNotifyServicesTest extends MediaWikiIntegrationTestCase {
    public function testGetEchoEventCreator(): void {
        $this->assertInstanceOf( EchoEventCreator::class, PegaNotifyServices::getEchoEventCreator() );
    }

    public function testGetNotificationRunner(): void {
        $this->assertInstanceOf( NotificationRunner::class, PegaNotifyServices::getNotificationRunner() );
    }

    public function testGetNotifierStore(): void {
        $this->assertInstanceOf( NotifierStore::class, PegaNotifyServices::getNotifierStore() );
    }

    public function testGetPushedNotificationBucket(): void {
        $this->assertInstanceOf( PushedNotificationBucket::class, PegaNotifyServices::getPushedNotificationBucket() );
    }
}