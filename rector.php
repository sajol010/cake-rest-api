<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Cake\Upgrade\Rector\Set\CakePHPSetList;
use Rector\ValueObject\PhpVersion;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        CakePHPSetList::CAKEPHP_52,
    ]);

    // Define paths to process
    $rectorConfig->paths([
        __DIR__ . '/src',
        __DIR__ . '/config',
        // __DIR__ . '/tests', // Uncomment if you have a tests directory at the root
    ]);

    // Uncomment if you need to skip specific directories or files
    // $rectorConfig->skip([
    //     __DIR__ . '/src/Vendor/',
    //     __DIR__ . '/config/Migrations/',
    // ]);

    // Set PHP version
    // CakePHP 5.2 requires PHP 8.1+
    $rectorConfig->phpVersion(PhpVersion::PHP_81);

    // You can also add individual rules or other sets
    // For example, if you want to enforce typed properties:
    // $rectorConfig->sets([
    //     \Rector\Set\ValueObject\SetList::TYPE_DECLARATION_STRICT,
    // ]);
};
# Test comment
