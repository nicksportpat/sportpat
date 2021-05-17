<?php
return [
    'backend' => [
        'frontName' => 'admin'
    ],
    'crypt' => [
        'key' => '624980bf4d91067795cda9315655e7bb'
    ],
    'db' => [
        'table_prefix' => '',
        'connection' => [
            'default' => [
                'host' => 'sportpat-mysql.cwtalbidsowa.us-east-1.rds.amazonaws.com',
                'dbname' => 'sportpat2',
                'username' => 'sportpat',
                'password' => 'NcW2#4%6',
                'active' => '1',
                'model' => 'mysql4',
                'engine' => 'innodb',
                'initStatements' => 'SET NAMES utf8;'
            ]
        ]
    ],
    'resource' => [
        'default_setup' => [
            'connection' => 'default'
        ]
    ],
    'x-frame-options' => 'SAMEORIGIN',
    'MAGE_MODE' => 'production',
    'session' => [
        'save' => 'redis',
        'redis' => [
            'host' => 'localhost',
            'port' => '6379',
            'password' => '',
            'timeout' => '3.5',
            'persistent_identifier' => 'dr1_',
            'database' => '10',
            'compression_threshold' => '2048',
            'compression_library' => 'gzip',
            'log_level' => '4',
            'max_concurrency' => '600',
            'break_after_frontend' => '5',
            'break_after_adminhtml' => '15',
            'first_lifetime' => '600',
            'bot_first_lifetime' => '60',
            'bot_lifetime' => '7200',
            'disable_locking' => '1',
            'min_lifetime' => '600',
            'max_lifetime' => '2592000'
        ]
    ],
    'cache' => [
        'frontend' => [
            'default' => [
                'id_prefix' => 'd69_',
                'backend' => 'Cm_Cache_Backend_Redis',
                'backend_options' => [
                    'server' => '127.0.0.1',
                    'database' => '0',
                    'port' => '6379',
                    'password' => ''
                ]
            ],
            'page_cache' => [
                'id_prefix' => 'd69_',
                'backend' => 'Cm_Cache_Backend_Redis',
                'backend_options' => [
                    'server' => '127.0.0.1',
                    'database' => '1',
                    'port' => '6379',
                    'compress_data' => '0',
                    'password' => ''
                ]
            ]
        ]
    ],
    'lock' => [
        'provider' => 'db',
        'config' => [
            'prefix' => ''
        ]
    ],
    'cache_types' => [
        'config' => 1,
        'layout' => 1,
        'block_html' => 1,
        'collections' => 1,
        'reflection' => 1,
        'db_ddl' => 1,
        'eav' => 1,
        'customer_notification' => 1,
        'config_integration' => 1,
        'config_integration_api' => 1,
        'full_page' => 1,
        'config_webservice' => 1,
        'translate' => 1,
        'vertex' => 0,
        'compiled_config' => 1,
        'amasty_shopby' => 0,
        'seoblock_cache_tag' => 1,
        'megamenu_items_cache_tag' => 0,
        'icons_cache_tag' => 1,
        'checkout' => 1
    ],
    'install' => [
        'date' => 'Tue, 18 Feb 2020 01:29:25 +0000'
    ],
    'downloadable_domains' => [
        'www.sportpat.com',
        'imagessportpat.s3-accelerate.amazonaws.com'
    ],
    'system' => [
        'default' => [
            'dev' => [
                'js' => [
                    'minify_files' => '1',
                    'enable_js_bundling' => '0',
                    'enable_baler_js_bundling' => '0',
                    'merge_files' => '0'
                ]
            ]
        ]
    ]
];
