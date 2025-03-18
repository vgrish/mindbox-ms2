<?php

declare(strict_types=1);

/**
 * Copyright (c) 2024 Vgrish <vgrish@gmail.com>
 * "vgrish/mindbox-ms2" package for MindBoxMS2
 * @see https://github.com/vgrish/mindbox-ms2
 */

$xpdo_meta_map['MindBoxMS2Event'] = [
    'package' => 'MindBoxMS2',
    'version' => '1.1',
    'table' => 'mindbox_ms2_events',
    'extends' => 'xPDOObject',
    'tableMeta' => [
        'engine' => 'InnoDB',
    ],
    'fields' => [
        'id' => 0,
        'operation' => '',
        'is_async_operation' => 0,
        'context_key' => 'web',
        'client_uuid' => '',
        'client_ip' => '',
        'sended' => 0,
        'rejected' => 0,
        'created_at' => null,
        'updated_at' => null,
        'sended_at' => null,
        'data' => null,
        'error' => null,
    ],
    'fieldMeta' => [
        'id' => [
            'dbtype' => 'bigint',
            'precision' => '20',
            'phptype' => 'integer',
            'null' => false,
            'attributes' => 'unsigned',
            'default' => 0,
            'index' => 'pk',
        ],
        'operation' => [
            'dbtype' => 'varchar',
            'precision' => '191',
            'phptype' => 'string',
            'null' => false,
            'default' => '',
        ],
        'is_async_operation' => [
            'dbtype' => 'tinyint',
            'precision' => '1',
            'phptype' => 'boolean',
            'attributes' => 'unsigned',
            'null' => false,
            'default' => 0,
        ],
        'context_key' => [
            'dbtype' => 'varchar',
            'precision' => '100',
            'phptype' => 'string',
            'null' => true,
            'default' => 'web',
        ],
        'client_uuid' => [
            'dbtype' => 'varchar',
            'precision' => '100',
            'phptype' => 'string',
            'null' => false,
            'default' => '',
        ],
        'client_ip' => [
            'dbtype' => 'varchar',
            'precision' => '100',
            'phptype' => 'string',
            'null' => false,
            'default' => '',
        ],
        'sended' => [
            'dbtype' => 'tinyint',
            'precision' => '1',
            'phptype' => 'boolean',
            'attributes' => 'unsigned',
            'null' => false,
            'default' => 0,
        ],
        'rejected' => [
            'dbtype' => 'tinyint',
            'precision' => '1',
            'phptype' => 'boolean',
            'attributes' => 'unsigned',
            'null' => false,
            'default' => 0,
        ],
        'created_at' => [
            'dbtype' => 'int',
            'precision' => '20',
            'phptype' => 'timestamp',
            'null' => true,
        ],
        'updated_at' => [
            'dbtype' => 'int',
            'precision' => '20',
            'phptype' => 'timestamp',
            'null' => true,
        ],
        'sended_at' => [
            'dbtype' => 'int',
            'precision' => '20',
            'phptype' => 'timestamp',
            'null' => true,
        ],
        'data' => [
            'dbtype' => 'text',
            'phptype' => 'json',
            'null' => true,
        ],
        'error' => [
            'dbtype' => 'text',
            'phptype' => 'string',
            'null' => true,
        ],
    ],
    'indexes' => [
        'PRIMARY' => [
            'alias' => 'PRIMARY',
            'primary' => true,
            'unique' => true,
            'type' => 'BTREE',
            'columns' => [
                'id' => [
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ],
            ],
        ],
        'operation' => [
            'alias' => 'operation',
            'primary' => false,
            'unique' => false,
            'type' => 'BTREE',
            'columns' => [
                'operation' => [
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ],
            ],
        ],
        'context_key' => [
            'alias' => 'context_key',
            'primary' => false,
            'unique' => false,
            'type' => 'BTREE',
            'columns' => [
                'context_key' => [
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ],
            ],
        ],
        'sended' => [
            'alias' => 'sended',
            'primary' => false,
            'unique' => false,
            'type' => 'BTREE',
            'columns' => [
                'sended' => [
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ],
            ],
        ],
        'rejected' => [
            'alias' => 'rejected',
            'primary' => false,
            'unique' => false,
            'type' => 'BTREE',
            'columns' => [
                'rejected' => [
                    'length' => '',
                    'collation' => 'A',
                    'null' => false,
                ],
            ],
        ],
    ],
];
