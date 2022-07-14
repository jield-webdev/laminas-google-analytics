<?php

namespace LaminasGoogleAnalyticsTest\Analytics;

use LaminasGoogleAnalytics\Analytics\Event;
use LaminasGoogleAnalytics\Analytics\Tracker;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    public function testCanInstantiateEvent(): void
    {
        $event = new Event('Category', 'Action');

        $this->assertEquals('Category', $event->getCategory());
        $this->assertEquals('Action', $event->getAction());
    }

    public function testCanAddEventToTracker(): void
    {
        $tracker = new Tracker(123);
        $event = new Event('Category', 'Action');
        $tracker->addEvent($event);

        $events = count($tracker->getEvents());
        $this->assertEquals(1, $events);
    }

    public function testCanAddMultipleEventsToTracker(): void
    {
        $tracker = new Tracker(123);
        $event1 = new Event('Category', 'Action');
        $event2 = new Event('Category', 'Action');
        $tracker->addEvent($event1);
        $tracker->addEvent($event2);

        $events = count($tracker->getEvents());
        $this->assertEquals(2, $events);
    }

    public function testCanHaveEventLabel(): void
    {
        $event = new Event('Category', 'Action', 'Label');

        $this->assertEquals('Label', $event->getLabel());
    }

    public function testCanHaveEventValue(): void
    {
        $event = new Event('Category', 'Action', null, 123);

        $this->assertEquals(123, $event->getValue());
    }

    public function testCanHaveEventLabelAndValue(): void
    {
        $event = new Event('Category', 'Action', 'Label', 123);

        $this->assertEquals('Label', $event->getLabel());
        $this->assertEquals(123, $event->getValue());
    }
}
