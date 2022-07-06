<?php

namespace PegaNotify\MediaWiki\Hooks;

use PegaNotify\Notifiers\Notifier;
use PegaNotify\PegaNotifyServices;

/**
 * Hook handler for Echo hooks implemented by PegaNotify.
 */
class EchoHooks {
    /**
     * Called when the events for Echo are retrieved and created.
     *
     * @param array $notifications
     *
     * @return void
     */
    public static function onBeforeCreateEchoEvent( array &$notifications ): void {
        foreach ( static::getNotifiers() as $notifier ) {
            $notifications[$notifier->getName()] = static::createDefinition( $notifier );
        }
    }

    /**
     * Returns the notifiers to register.
     *
     * @return Notifier[]
     */
    private static function getNotifiers(): array {
        // FIXME: Use DI whenever Echo is updated to use the new hook system
        return PegaNotifyServices::getNotifierStore()->getNotifiers();
    }

    /**
     * Creates a notification definition for the given notifier.
     *
     * @param Notifier $notifier
     * @return array
     */
    private static function createDefinition(Notifier $notifier ): array {
        $definition = [
            'category' => 'system',
            'section' => 'alert',
            'group' => 'negative',
            'bundle' => [
                'web' => true,
                'expandable' => true,
            ]
        ];

        $definition['presentation-model'] = $notifier->getPresentationModel();
        $definition['user-locators'] = [$notifier, 'getNotificationUsers'];

        return $definition;
    }
}