<?php

namespace PegaNotify\Notifiers;

use ExtensionRegistry;
use MWTimestamp;
use SMW\Query\PrintRequest;
use SMW\Query\QueryContext;
use SMW\StoreFactory;
use SMWQuery;
use SMWQueryProcessor;
use Title;
use User;
use Wikimedia\Timestamp\TimestampException;

/**
 * Implements the "expiring soon" notifier. This notifier is triggered when a page's verified status is about to
 * expire soon. Notifications are sent on the following schedule:
 *
 * - 2 weeks before expiration
 * - 1 week before expiration
 * - 1 day before expiration
 *
 * @see https://pegadigitalit.atlassian.net/browse/KHUB-981
 */
abstract class ExpiringSoonNotifier extends Notifier {
    // The key used for the "time remaining" data item
    public const TIME_REMAINING_EXTRA_KEY = "time-remaining";

    // The name of the property that contains the date until which a page is verified
    private const VERIFIED_UNTIL_PROPERTY_KEY = "Verification lifespan date";

    // The name of the property that contains the owner of the page
    private const PAGE_OWNER_PROPERTY_KEY = "Page owner";

    // The times at which to notify a user (in days)
    private const NOTIFY_BEFORE_EXPIRATION_DAYS = [14, 7, 1];

    /**
     * @var array[] Cache of computed notifications
     */
    private static array $notificationsCache;

    /**
     * @var <string, User[]>[] Cache of page owners
     */
    private static array $pageOwnersCache = [];

    /**
     * @inheritDoc
     */
    public function getIcons(): array {
        return [
            'peganotify-expiring-soon' => [
                'path' => 'PegaNotify/modules/icons/expiring-soon.svg'
            ]
        ];
    }

    /**
     * @inheritDoc
     * @throws TimestampException
     */
    public function getNotifications(): array {
        if ( !ExtensionRegistry::getInstance()->isLoaded( 'SemanticMediaWiki' ) ) {
            // Make sure SemanticMediaWiki is installed and enabled
            return [];
        }

        if ( isset( self::$notificationsCache ) ) {
            return self::$notificationsCache;
        }

        self::$notificationsCache = [];

        foreach ( self::NOTIFY_BEFORE_EXPIRATION_DAYS as $id => $maxDaysBeforeExpiration ) {
            // We want to build a query that returns all pages between "max days", and the next "max days". So, if the
            // NOTIFY_BEFORE_EXPIRATION_DAYS array has items [14, 7, 1], we build queries for the following date spans:
            //
            // - 14 days before until 7 days before
            // - 7 days before until 1 day before
            // - 1 day before until 0 days before
            $minDaysBeforeExpiration = self::NOTIFY_BEFORE_EXPIRATION_DAYS[$id + 1] ?? 0;
            $smwQuery = self::buildSemanticQuery( $maxDaysBeforeExpiration, $minDaysBeforeExpiration );
            $resultSet = StoreFactory::getStore()->getQueryResult( $smwQuery )->serializeToArray();

            $results = $resultSet["results"] ?? [];

            foreach ( $results as $titleText => $result ) {
                if ( !isset( $result["printouts"][self::VERIFIED_UNTIL_PROPERTY_KEY][0]["timestamp"] ) ) {
                    // Although the query should guarantee this property is set, we don't want to crash if it isn't
                    continue;
                }

                $pageTitle = Title::newFromText( $titleText );

                if ( $pageTitle === null || !$pageTitle->exists() ) {
                    // Although the query should guarantee the title exists, we don't want to crash if it doesn't
                    continue;
                }

                // Get the timestamp (Unix) until which the page is verified
                $verifiedUntilTimestamp = $result["printouts"][self::VERIFIED_UNTIL_PROPERTY_KEY][0]["timestamp"];

                // Insert the notification
                self::$notificationsCache[] = [
                    // Build a unique ID, for example "562-1657756800-14d"
                    'id' => sprintf(
                        "%s-%s-%sd",
                        $pageTitle->getArticleID(),
                        $verifiedUntilTimestamp,
                        $maxDaysBeforeExpiration
                    ),
                    'data' => [
                        'title' => $pageTitle,
                        'extra' => [
                            self::TIME_REMAINING_EXTRA_KEY => $maxDaysBeforeExpiration
                        ]
                    ]
                ];
            }
        }

        return self::$notificationsCache;
    }

    /**
     * Returns the page owners of the given Title.
     *
     * @param Title $title The title to get the page owner for
     * @return User[]
     */
    protected static function getPageOwners( Title $title ): array {
        if ( !ExtensionRegistry::getInstance()->isLoaded( 'SemanticMediaWiki' ) ) {
            // Make sure SemanticMediaWiki is installed and enabled
            return [];
        }

        $pageTitle = $title->getFullText();

        if ( isset( self::$pageOwnersCache[$pageTitle] ) ) {
            return self::$pageOwnersCache[$pageTitle];
        }

        $resultSet = StoreFactory::getStore()->getQueryResult( SMWQueryProcessor::createQuery(
            sprintf( '[[%s]]', $pageTitle ),
            SMWQueryProcessor::getProcessedParams( ['limit' => 5000] ),
            QueryContext::DEFERRED_QUERY,
            'table',
            [PrintRequest::newFromText( self::PAGE_OWNER_PROPERTY_KEY )]
        ) )->serializeToArray();

        $propertyValues = $resultSet["results"][$pageTitle]['printouts'][self::PAGE_OWNER_PROPERTY_KEY] ?? [];
        self::$pageOwnersCache[$pageTitle] = [];

        foreach ( $propertyValues as $value ) {
            if ( is_array( $value ) && isset( $value['fulltext'] ) ) {
                $pageOwner = $value['fulltext'];
            } elseif ( is_string( $value ) ) {
                $pageOwner = $value;
            } else {
                continue;
            }

            // The page owner is a page (that should be in the User namespace)
            $pageOwnerTitle = Title::newFromText( $pageOwner );

            if ( !$pageOwnerTitle->inNamespace( NS_USER ) ) {
                continue;
            }

            $pageOwnerUser = User::newFromName( $pageOwnerTitle->getText() );

            if ( $pageOwnerUser === null || $pageOwnerUser->isAnon() ) {
                continue;
            }

            self::$pageOwnersCache[$pageTitle][] = $pageOwnerUser;
        }

        return self::$pageOwnersCache[$pageTitle];
    }

    /**
     * Builds an SMW query that returns all pages that are between $daysBeforeExpirationMax and $daysBeforeExpirationMin
     * days before the expiration date of the corresponding page.
     *
     * @param int $maxDaysBeforeExpiration The maximum number of days before the expiration date
     * @param int $minDaysBeforeExpiration The minimum number of days before the expiration date
     * @return SMWQuery
     * @throws TimestampException
     */
    private static function buildSemanticQuery( int $maxDaysBeforeExpiration, int $minDaysBeforeExpiration ): SMWQuery {
        $queryString = sprintf(
            '[[' . self::VERIFIED_UNTIL_PROPERTY_KEY . '::>=%s]] [[' . self::VERIFIED_UNTIL_PROPERTY_KEY . '::<<%s]]',
            self::getDaysFromNow( $minDaysBeforeExpiration ),
            self::getDaysFromNow( $maxDaysBeforeExpiration )
        );

        return SMWQueryProcessor::createQuery(
            $queryString,
            // Set a high limit to make sure we are not missing pages
            SMWQueryProcessor::getProcessedParams( ['limit' => 5000] ),
            QueryContext::DEFERRED_QUERY,
            'table',
            [PrintRequest::newFromText( self::VERIFIED_UNTIL_PROPERTY_KEY )]
        );
    }

    /**
     * Returns a date that is the given number of days from now, in the local timezone (set by MediaWiki).
     *
     * @param int $daysFromNow The number of days from now to get the timestamp for
     * @param string $format The format in which to return the date
     * @return string
     * @throws TimestampException
     */
    private static function getDaysFromNow( int $daysFromNow, string $format = 'Y-m-d' ): string {
        // Get a new local timestamp instance
        $timestamp = MWTimestamp::getLocalInstance();

        // Move the timestamp $daysFromNow forward
        $timestamp->setTimestamp( time() + (60 * 60 * 24 * $daysFromNow) );

        // Format and return the timestamp
        return $timestamp->format( $format );
    }
}