<?php

namespace PegaNotify\Tests\Unit;

use MediaWikiUnitTestCase;
use PegaNotify\Notifiers\Notifier;
use PegaNotify\NotifierStore;

/**
 * @covers \PegaNotify\NotifierStore
 */
class NotifierStoreTest extends MediaWikiUnitTestCase {
    public function setUp(): void {
        $this->notifierStore = new NotifierStore();
    }

    public function testGetNotifierClasses(): void {
        $notifierClasses = $this->notifierStore->getNotifierClasses();

        foreach ( $notifierClasses as $notifierClass ) {
            $instance = new $notifierClass();

            $this->assertInstanceOf( Notifier::class, $instance, 'Classes returned from NotifierStore::getNotifierClasses must extend Notifier' );
        }
    }

    public function testGetNotifiers(): void {
        $notifiers = $this->notifierStore->getNotifiers();

        foreach ( $notifiers as $instance ) {
            $this->assertInstanceOf( Notifier::class, $instance, 'Instances returned from NotifierStore::getNotifiers must extend Notifier' );
        }
    }
}