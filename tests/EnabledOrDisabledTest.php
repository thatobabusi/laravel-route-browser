<?php

namespace RouteBrowserTests;

class EnabledOrDisabledTest extends TestCase
{
    public function testExplicitlyEnabled()
    {
        config(['route-browser.enabled' => true]);

        $this->assertEnabled();
    }

    public function testExplicitlyDisabled()
    {
        config(['route-browser.enabled' => false]);

        $this->assertDisabled();
    }

    public function testDisabledByDefault()
    {
        $this->assertDisabled();
    }

    public function testEnabledWhenDebuggingEnabledLocally()
    {
        config(['app.debug' => true, 'app.env' => 'local']);

        $this->assertEnabled();
    }

    public function testDisabledWhenDebuggingIsOff()
    {
        config(['app.env' => 'local']);

        $this->assertDisabled();
    }

    public function testDisabledWhenNotLocal()
    {
        config(['app.debug' => true]);

        $this->assertDisabled();
    }

    private function assertDisabled()
    {
        $this->getRouteList()->assertForbidden();
    }

    private function assertEnabled()
    {
        $this->getRouteList()->assertOk();
    }

    /** @noinspection ReturnTypeCanBeDeclaredInspection Changed between Laravel 6 and 7 */
    private function getRouteList()
    {
        return $this->withExceptionHandling()->get('/routes');
    }
}
