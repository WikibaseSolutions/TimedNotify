<?php

namespace PegaNotify\Tests\Integration;

use IDatabase;
use MediaWikiIntegrationTestCase;
use PegaNotify\EchoEventCreator;
use PegaNotify\NotificationRunner;
use PegaNotify\Notifiers\Notifier;
use PegaNotify\NotifierStore;
use PegaNotify\PegaNotifyServices;
use PegaNotify\PushedNotificationBucket;

/**
 * @covers \PegaNotify\NotificationRunner
 */
class NotificationRunnerTest extends MediaWikiIntegrationTestCase {
    public function tearDown(): void {
        wfGetDB( DB_PRIMARY )->delete( PushedNotificationBucket::PUSHED_NOTIFICATIONS_TABLE, IDatabase::ALL_ROWS );
    }

    public function testRunOccasionallyRunsOccasionally(): void {
        $numTries = 500;

        $notifierStoreMock = $this->getMockBuilder(NotifierStore::class)->getMock();

        $notifierStoreMock->expects($this->atLeastOnce())->method('getNotifiers');
        $notifierStoreMock->expects($this->atMost($numTries - 1))->method('getNotifiers');

        $notifierStoreMock->method('getNotifiers')->willReturn([]);

        $notificationRunner = new NotificationRunner(
            $notifierStoreMock,
            PegaNotifyServices::getPushedNotificationBucket(),
            PegaNotifyServices::getEchoEventCreator(),
            0.5
        );

        for ($i = 0; $i < $numTries; $i++) {
            $notificationRunner->runOccasionally( false );
        }
    }

    public function testRunOccasionallyRunsAlwaysWhenRunRateIsOne(): void {
        $numTries = 500;

        $notifierStoreMock = $this->getMockBuilder(NotifierStore::class)->getMock();
        $notifierStoreMock->expects($this->exactly($numTries))->method('getNotifiers');

        $notifierStoreMock->method('getNotifiers')->willReturn([]);

        $notificationRunner = new NotificationRunner(
            $notifierStoreMock,
            PegaNotifyServices::getPushedNotificationBucket(),
            PegaNotifyServices::getEchoEventCreator(),
            1.0
        );

        for ($i = 0; $i < $numTries; $i++) {
            $notificationRunner->runOccasionally( false );
        }
    }

    public function testRunOccasionallyRunsNeverWhenRunRateIsZero(): void {
        $notifierStoreMock = $this->getMockBuilder(NotifierStore::class)->getMock();
        $notifierStoreMock->expects($this->never())->method('getNotifiers');

        $notifierStoreMock->method('getNotifiers')->willReturn([]);

        $notificationRunner = new NotificationRunner(
            $notifierStoreMock,
            PegaNotifyServices::getPushedNotificationBucket(),
            PegaNotifyServices::getEchoEventCreator(),
            0.0
        );

        for ($i = 0; $i < 500; $i++) {
            $notificationRunner->runOccasionally( false );
        }
    }

    public function testRunAddsNotificationToBucketIfItHasId(): void {
        $notifierMock = $this->getMockBuilder( Notifier::class )->getMock();
        $notifierMock->method( 'getNotifications' )->willReturn( [
           [
               'id' => 'testing',
               'data' => []
           ]
        ] );
        $notifierMock->method( 'getName' )->willReturn( 'TestingNotifier' );

        $notifierStoreMock = $this->getMockBuilder( NotifierStore::class )->getMock();
        $notifierStoreMock->method( 'getNotifiers' )->willReturn( [
            $notifierMock
        ] );

        $bucket = PegaNotifyServices::getPushedNotificationBucket();

        $this->assertFalse( $bucket->isPushed( 'TestingNotifier-testing' ) );

        $notificationRunner = new NotificationRunner(
            $notifierStoreMock,
            $bucket,
            PegaNotifyServices::getEchoEventCreator(),
            1.0
        );

        $notificationRunner->run();

        $this->assertTrue( $bucket->isPushed( 'TestingNotifier-testing' ) );
    }

    public function testRunDoesNotAddNotificationIfIdIsNull(): void {
        $notifierMock = $this->getMockBuilder( Notifier::class )->getMock();
        $notifierMock->method( 'getNotifications' )->willReturn( [
            [
                'data' => []
            ]
        ] );
        $notifierMock->method( 'getName' )->willReturn( 'TestingNotifier' );

        $notifierStoreMock = $this->getMockBuilder( NotifierStore::class )->getMock();
        $notifierStoreMock->method( 'getNotifiers' )->willReturn( [
            $notifierMock
        ] );

        $bucketMock = $this->getMockBuilder( PushedNotificationBucket::class )->disableOriginalConstructor()->getMock();
        $bucketMock->expects( $this->never() )->method( 'setPushed' );

        $notificationRunner = new NotificationRunner(
            $notifierStoreMock,
            $bucketMock,
            PegaNotifyServices::getEchoEventCreator(),
            1.0
        );

        $notificationRunner->run();
    }

    public function testRunPurgesOld(): void {
        $bucketMock = $this->getMockBuilder( PushedNotificationBucket::class )->disableOriginalConstructor()->getMock();
        $bucketMock->expects( $this->once() )->method( 'purgeOld' );

        $notificationRunner = new NotificationRunner(
            PegaNotifyServices::getNotifierStore(),
            $bucketMock,
            PegaNotifyServices::getEchoEventCreator(),
            1.0
        );

        $notificationRunner->run();
    }

    public function testRunSkipsPushedNotifications(): void {
        $notifierMock = $this->getMockBuilder( Notifier::class )->getMock();
        $notifierMock->method( 'getNotifications' )->willReturn( [
            [
                'id' => 'testing',
                'data' => []
            ]
        ] );
        $notifierMock->method( 'getName' )->willReturn( 'TestingNotifier' );

        $notifierStoreMock = $this->getMockBuilder( NotifierStore::class )->getMock();
        $notifierStoreMock->method( 'getNotifiers' )->willReturn( [
            $notifierMock
        ] );

        $pushedNotificationsBucket = PegaNotifyServices::getPushedNotificationBucket();
        $pushedNotificationsBucket->setPushed( 'TestingNotifier-testing' );

        $echoEventCreatorMock = $this->getMockBuilder( EchoEventCreator::class )->getMock();
        $echoEventCreatorMock->expects( $this->never() )->method( 'create' );

        $notificationRunner = new NotificationRunner(
            $notifierStoreMock,
            $pushedNotificationsBucket,
            $echoEventCreatorMock,
            1.0
        );

        $notificationRunner->run();
    }

    public function testRunNotificationWithSameIdIsPushedExactlyOnce(): void {
        $notifierMock = $this->getMockBuilder( Notifier::class )->getMock();
        $notifierMock->method( 'getNotifications' )->willReturn( [
            [
                'id' => 'testing',
                'data' => []
            ]
        ] );

        $notifierMock->method( 'getName' )->willReturn( 'TestingNotifier' );

        $notifierStoreMock = $this->getMockBuilder( NotifierStore::class )->getMock();
        $notifierStoreMock->method( 'getNotifiers' )->willReturn( [
            $notifierMock
        ] );

        $echoEventCreatorMock = $this->getMockBuilder( EchoEventCreator::class )->getMock();
        $echoEventCreatorMock->expects( $this->once() )->method( 'create' );

        $notificationRunner = new NotificationRunner(
            $notifierStoreMock,
            PegaNotifyServices::getPushedNotificationBucket(),
            $echoEventCreatorMock,
            1.0
        );

        $notificationRunner->run();

        $echoEventCreatorMock = $this->getMockBuilder( EchoEventCreator::class )->getMock();
        $echoEventCreatorMock->expects( $this->never() )->method( 'create' );

        $notificationRunner = new NotificationRunner(
            $notifierStoreMock,
            PegaNotifyServices::getPushedNotificationBucket(),
            $echoEventCreatorMock,
            1.0
        );

        $notificationRunner->run();
    }
}