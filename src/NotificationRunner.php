<?php

namespace PegaNotify;

use DeferredUpdates;
use EchoEvent;
use MWException;

/**
 * This class is responsible for running notifications.
 */
class NotificationRunner {
    /**
     * @var NotifierStore The notifier store to use
     */
    private NotifierStore $notifierStore;

    /**
     * @var PushedNotificationBucket The bucket of pushed notifications
     */
    private PushedNotificationBucket $pushedNotificationBucket;

    /**
     * @var float The run rate, as configured through $wgPegaNotifyRunRate (between 0 and 1, inclusive)
     */
    private float $runRate;

    /**
     * @param NotifierStore $notifierStore The notifier store to use
     * @param float $runRate The run rate, as configured through $wgPegaNotifyRunRate (will be limited to 1)
     */
    public function __construct( NotifierStore $notifierStore, PushedNotificationBucket $pushedNotificationBucket, float $runRate = 1.0 ) {
        $this->notifierStore = $notifierStore;
        $this->pushedNotificationBucket = $pushedNotificationBucket;

        $this->runRate = min( 1, $runRate );
    }

    /**
     * Run the notifications immediately.
     *
     * @return void
     * @throws MWException
     */
    public function run(): void {
        $notifiers = $this->notifierStore->getNotifiers();

        foreach ( $notifiers as $notifier ) {
            // Get the pages for which to push notifications
            $pages = $notifier->getPages();

            // Push a notification for each page
            foreach ( $pages as $id => $title ) {
                $id = !is_int( $id ) ? sprintf( "%s-%s", $notifier->getName(), $id ) : null;

                if ( $id !== null && $this->pushedNotificationBucket->isPushed( $id ) ) {
                    continue;
                }

                $notificationData = $notifier->getNotificationData( $title );
                $notificationData['type'] = $notifier->getName();

                EchoEvent::create( $notificationData );

                if ( $id !== null ) {
                    $this->pushedNotificationBucket->setPushed( $id );
                }
            }
        }

        // Purge any old pushed pages from the bucket (is there a better place to do this?)
        $this->pushedNotificationBucket->purgeOld();
    }

    /**
     * Run the notifications in a deferred request.
     *
     * @return void
     */
    public function runDeferred(): void {
        DeferredUpdates::addCallableUpdate( function () {
            $this->run();
        } );
    }

    /**
     * Run the notifications opportunistically (and deferred).
     *
     * To prevent slowness and unnecessary overhead, notifications are only run sometimes. This function may therefore
     * do nothing when called, or it may on occasion run the notifications. How often this function actually runs the
     * notifications is dependent on the value of $wgPegaNotifyRunRate.
     *
     * When the run rate is a number between 0 and 1, the notifications are run, on average, every 1/runRate times this
     * function is called. For example, if the run rate is 0.01, the notifications are run about every 100 times this
     * function is called. That is, the probability that the notifications are run when this function is called is 1 in
     * 100. In theory, the notifications could be run for each time this function is called, or they can never be run.
     * However, in practice this does not happen. If the run rate is greater or equal to 1, the notifications are always
     * run when this function is called. If the run rate is set to 0, notifications are never run when this function is
     * called.
     *
     * @see NotificationRunner::run() for a function that always immediately runs the notifications
     * @see NotificationRunner::runDeferred() for a function that always runs the notifications in a deferred request
     *
     * @return void
     */
    public function runOpportunistic(): void {
        // Generate a random value between 0 and 1 inclusive
        $rand = lcg_value();

        if ( $rand <= $this->runRate ) {
            // Bingo! Run the notifications
            // $this->runDeferred();
            $this->run();
        }
    }
}