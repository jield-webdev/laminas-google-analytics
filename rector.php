<?php

declare(strict_types=1);

use Rector\Core\ValueObject\PhpVersion;
use Rector\Php80\Rector\Class_\DoctrineAnnotationClassToAttributeRector;

return static function (\Rector\Config\RectorConfig $rectorConfig): void {
    $rectorConfig->paths(
        [
            __DIR__ . '/src'
        ]
    );
    $rectorConfig->importNames();
    $rectorConfig->phpVersion(PhpVersion::PHP_80);
    $rectorConfig->sets([
        \Rector\Set\ValueObject\LevelSetList::UP_TO_PHP_80
    ]);

    $rectorConfig->ruleWithConfiguration(\Rector\Php74\Rector\Property\TypedPropertyRector::class, [
        \Rector\Php74\Rector\Property\TypedPropertyRector::INLINE_PUBLIC => false
    ]);
    $rectorConfig->ruleWithConfiguration(DoctrineAnnotationClassToAttributeRector::class, [
        DoctrineAnnotationClassToAttributeRector::REMOVE_ANNOTATIONS => true,
    ]);

    // get services (needed for register a single rule)
    // $services = $containerConfigurator->services();

    // register a single rule
    // $services->set(TypedPropertyRector::class);
};
