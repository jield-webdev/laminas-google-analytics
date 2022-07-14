<?php

namespace LaminasGoogleAnalyticsTest\Analytics;

use LaminasGoogleAnalytics\Analytics\Tracker;
use PHPUnit\Framework\TestCase;

class TrackerTest extends TestCase
{

    public function testCanInstantiateTracker(): void
    {
        $tracker = new Tracker(123);
        $this->assertEquals(123, $tracker->getId());
    }

    public function testIsEnabledByDefault(): void
    {
        $tracker = new Tracker(123);
        $this->assertTrue($tracker->enabled());
    }

    public function testHasPageTrackingEnabledByDefault(): void
    {
        $tracker = new Tracker(123);
        $this->assertTrue($tracker->enabledPageTracking());
    }

    public function testDomainNameDefaultsToFalse(): void
    {
        $tracker = new Tracker(123);
        $this->assertNull($tracker->getDomainName());
    }

    public function testDomainName(): void
    {
        $tracker = new Tracker(123);
        $tracker->setDomainName('foobar');
        $this->assertEquals('foobar', $tracker->getDomainName());
    }

    public function testClearDomainNameReturnsNull(): void
    {
        $tracker = new Tracker(123);
        $tracker->setDomainName('foobar');
        $tracker->clearDomainName();
        $this->assertNull($tracker->getDomainName());
    }

    public function testAllowLinkerDefaultsToFalse(): void
    {
        $tracker = new Tracker(123);
        $tracker->setAllowLinker(true);
        $this->assertTrue($tracker->getAllowLinker());
    }

    public function testEnableDisplayAdvertisingDefaultsToFalse(): void
    {
        $tracker = new Tracker(123);
        $this->assertFalse($tracker->getEnableDisplayAdvertising());
    }

    public function testAnonymizeIpDefaultsToFalse(): void
    {
        $tracker = new Tracker(123);
        $this->assertFalse($tracker->getAnonymizeIp());
    }

    public function testAnonymizeIp(): void
    {
        $tracker = new Tracker(123);
        $tracker->setAnonymizeIp(true);
        $this->assertTrue($tracker->getAnonymizeIp());
    }
}
