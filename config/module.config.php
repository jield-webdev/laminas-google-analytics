<?php

use LaminasGoogleAnalytics\Analytics\Tracker;
use LaminasGoogleAnalytics\Service\GoogleAnalyticsFactory;
use LaminasGoogleAnalytics\Service\ScriptFactory;
use LaminasGoogleAnalytics\Service\TrackerFactory;
use LaminasGoogleAnalytics\View\Helper\Script\Analyticsjs;
use LaminasGoogleAnalytics\View\Helper\Script\Gajs;

return [
    'google_analytics' =>
        [
            'enable' => true,
            'id' => '',
            'domain_name' => '',
            'allow_linker' => false,
            'enable_display_advertising' => false,
            'anonymize_ip' => false,
            'script' => 'google-analytics-ga'
        ],
    'service_manager' => [
        'aliases' => [
            'google-analytics' => Tracker::class,
            'google-analytics-universal' => Analyticsjs::class,
            'google-analytics-ga' => Gajs::class
        ],
        'invokables' => [
            Analyticsjs::class => Analyticsjs::class,
            Gajs::class => Gajs::class
        ],
        'factories' => [
            Tracker::class => TrackerFactory::class,
            ScriptFactory::class => ScriptFactory::class
        ]
    ],
    'view_helpers' => ['factories' => ['googleAnalytics' => GoogleAnalyticsFactory::class]]
];
