<?php
/**
 * Example configuration for Elasticsearch caching
 */
return [
    'stores' => [
        'elasticsearch' => [
            'driver'        => 'elasticsearch',
            /** index name */
            'index'         => 'bv_cache',
            /** optional document type */
            'type'          => null,
            'hosts'         => ['http://elastic:changeme@localhost:9200',],
            'strict-search' => false,
        ],
    ],
];
