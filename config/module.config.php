<?php

use LaminasGoogleAnalytics\Analytics\Tracker;
use LaminasGoogleAnalytics\Service\GoogleAnalyticsFactory;
use LaminasGoogleAnalytics\Service\ScriptFactory;
use LaminasGoogleAnalytics\Service\TrackerFactory;
use LaminasGoogleAnalytics\View\Helper\Script\Gajs;

return [
    'google_analytics' =>
        [
            'enable' => true,
            'id' => '',
        ],
    'service_manager' => [
        'aliases' => [
            'google-analytics' => Tracker::class,
            'google-analytics-ga' => Gajs::class
        ],
        'invokables' => [
            Gajs::class => Gajs::class
        ],
        'factories' => [
            Tracker::class => TrackerFactory::class,
            ScriptFactory::class => ScriptFactory::class
        ]
    ],
    'view_helpers' => [
        'factories' => [
            'googleAnalytics' => GoogleAnalyticsFactory::class
        ]
    ]
];
