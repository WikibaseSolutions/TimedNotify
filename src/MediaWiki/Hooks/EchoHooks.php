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
    public static function onBeforeCreateEchoEvent(
        array &$notifications,
        array &$notificationCategories,
        array &$icons
    ): void {
        foreach ( PegaNotifyServices::getNotifierStore()->getNotifierClasses() as $notifierClass ) {
            $notifications[$notifierClass::getName()] = static::createDefinition( $notifierClass );
            $icons = array_merge($icons, $notifierClass::getIcons());
        }
    }

    /**
     * Creates a notification definition for the given notifier.
     *
     * @param string|Notifier $notifierClass The notifier class to get the definition for
     * @return array
     */
    private static function createDefinition( string $notifierClass ): array {
        $definition = [
            'category' => 'system',
            'section' => 'alert',
            'group' => 'negative'
        ];

        $definition['presentation-model'] = $notifierClass::getPresentationModel();
        $definition['user-locators'] = [$notifierClass . '::getNotificationUsers'];

        return $definition;
    }
}