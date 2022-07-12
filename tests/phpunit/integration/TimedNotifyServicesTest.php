<?php

namespace TimedNotify\Tests\Integration;

use MediaWikiIntegrationTestCase;
use TimedNotify\EchoEventCreator;
use TimedNotify\NotificationRunner;
use TimedNotify\NotifierStore;
use TimedNotify\TimedNotifyServices;
use TimedNotify\PushedNotificationBucket;

/**
 * @covers \TimedNotify\TimedNotifyServices
 */
class TimedNotifyServicesTest extends MediaWikiIntegrationTestCase {
    public function testGetEchoEventCreator(): void {
        $this->assertInstanceOf( EchoEventCreator::class, TimedNotifyServices::getEchoEventCreator() );
    }

    public function testGetNotificationRunner(): void {
        $this->assertInstanceOf( NotificationRunner::class, TimedNotifyServices::getNotificationRunner() );
    }

    public function testGetNotifierStore(): void {
        $this->assertInstanceOf( NotifierStore::class, TimedNotifyServices::getNotifierStore() );
    }

    public function testGetPushedNotificationBucket(): void {
        $this->assertInstanceOf( PushedNotificationBucket::class, TimedNotifyServices::getPushedNotificationBucket() );
    }
}