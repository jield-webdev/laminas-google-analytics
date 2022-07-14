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
}
