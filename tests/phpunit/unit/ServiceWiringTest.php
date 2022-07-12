<?php

namespace PegaNotify\Tests\Unit;

use ExtensionRegistry;
use MediaWikiUnitTestCase;

/**
 * @coversNothing
 */
class ServiceWiringTest extends MediaWikiUnitTestCase {
    /**
     * @coversNothing
     */
    public function testServicesSortedAlphabetically() {
        $servicesNames = $this->getServicesNames();
        $sortedServices = $servicesNames;
        natcasesort( $sortedServices );

        $this->assertSame( $sortedServices, $servicesNames,
            'Please keep services names sorted alphabetically' );
    }

    /**
     * @coversNothing
     */
    public function testServicesArePrefixed() {
        $servicesNames = $this->getServicesNames();

        foreach ( $servicesNames as $serviceName ) {
            $this->assertStringStartsWith( 'PegaNotify.', $serviceName,
                'Please prefix services names with "PegaNotify."' );
        }
    }

    /**
     * Returns the names of all WikiGuard services.
     *
     * @return array
     */
    private function getServicesNames(): array {
        $allThings = ExtensionRegistry::getInstance()->getAllThings();
        $dirName = dirname( $allThings['PegaNotify']['path'] );

        return array_keys( require $dirName . '/PegaNotify.wiring.php' );
    }
}
