<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2019 BuzzingPixel, LLC
 * @license Apache-2.0
 */

return [
    'paths' => [
        // 'migrations' => '%%PHINX_CONFIG_DIR%%/src/migrations',
        'migrations' => '%%PHINX_CONFIG_DIR%%/tmp/migrations',
        'seeds' => '%%PHINX_CONFIG_DIR%%/src/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'migrations',
        'default_database' => 'production',
        'production' => [
            'adapter' => 'mysql',
            'host' => 'db',
            'name' => 'site',
            'user' => 'site',
            'pass' => 'secret',
            'port' => '3306',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
        ],
    ],
    'version_order' => 'creation'
];
