<?php

return [
    /*
    |--------------------------------------------------------------------------
    | QueryAdapter Connection Adapter
    |--------------------------------------------------------------------------
    |
    | Default query adapter connections. default connections is rabbitmq and elasticsearch.
    |
    */
    'connection' => [
        /*
         * Elasticsearch hosts in format http[s]://[user][:pass]@hostname[:9200]
         */
        'hosts' => explode(',', env('ELASTICSEARCH_HOSTS', '')),
        'retries' => env('ELASTICSEARCH_RETRIES', 1),
        'username' => env('ELASTICSEARCH_USERNAME', ''),
        'password' => env('ELASTICSEARCH_PASSWORD', ''),
        'ssl_verification' => env('ELASTICSEARCH_SSL_VERIFICATION', false),
        // The default client is Symfony\Component\HttpClient\Psr18Client. If you use another library, enter the class path completely
        'http_client' => env('ELASTICSEARCH_HTTP_CLIENT', 'Symfony\Component\HttpClient\Psr18Client'),
        // for call_user_func_array
        'http_client_options' => env('ELASTICSEARCH_HTTP_CLIENT_OPTIONS', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | QueryAdapter Queue Connection
    |--------------------------------------------------------------------------
    |
    | Default Queue connections rabbitmq.
    |
    */
    'queue' => [
        'default' => env('QUERY_ADAPTER_QUEUE_CONNECTION', 'sync'),

        'connections' => [
            'rabbitmq' => [
                'driver' => 'rabbitmq',
                'queue' => env('QUERY_ADAPTER_RABBITMQ_QUEUE', 'default'),
                'connection' => PhpAmqpLib\Connection\AMQPLazyConnection::class,

                'hosts' => [
                    [
                        'host' => env('QUERY_ADAPTER_RABBITMQ_HOST', 'localhost'),
                        'port' => env('QUERY_ADAPTER_RABBITMQ_PORT', 5672),
                        'username' => env('QUERY_ADAPTER_RABBITMQ_USERNAME', ''),
                        'password' => env('QUERY_ADAPTER_RABBITMQ_PASSWORD', ''),
                        'vhost' => env('QUERY_ADAPTER_RABBITMQ_VHOST', '/'),
                        'ssl_options' => [],
                        'ssl_protocol' => null,
                        'connect_options' => [],
                    ],
                ],

                'options' => [
                    'channel_id' => null,

                    'message' => [
                        'content_encoding' => 'UTF-8',
                        'content_type' => 'text/plain',
                        'delivery_mode' => env(
                            'QUERY_ADAPTER_RABBITMQ_MESSAGE_DELIVERY_MODE',
                            \PhpAmqpLib\Message\AMQPMessage::DELIVERY_MODE_PERSISTENT
                        ),
                    ],

                    'exchange' => [
                        'name' => env('QUERY_ADAPTER_RABBITMQ_EXCHANGE_NAME', 'amq.topic'),
                        'declare' => env('QUERY_ADAPTER_RABBITMQ_EXCHANGE_DECLARE', false),
                        'type' => env('QUERY_ADAPTER_RABBITMQ_EXCHANGE_TYPE', PhpAmqpLib\Exchange\AMQPExchangeType::DIRECT),
                        'passive' => env('QUERY_ADAPTER_RABBITMQ_EXCHANGE_PASSIVE', false),
                        'durable' => env('QUERY_ADAPTER_RABBITMQ_EXCHANGE_DURABLE', true),
                        'auto_delete' => env('QUERY_ADAPTER_RABBITMQ_EXCHANGE_AUTO_DEL', false),
                        'internal' => env('QUERY_ADAPTER_RABBITMQ_EXCHANGE_INTERNAL', false),
                        'nowait' => env('QUERY_ADAPTER_RABBITMQ_EXCHANGE_NOWAIT', false),
                        'properties' => [],
                    ],

                    'queue' => [
                        'declare' => env('QUERY_ADAPTER_RABBITMQ_QUEUE_DECLARE', false),
                        'passive' => env('QUERY_ADAPTER_RABBITMQ_QUEUE_PASSIVE', false),
                        'durable' => env('QUERY_ADAPTER_RABBITMQ_QUEUE_DURABLE', true),
                        'exclusive' => env('QUERY_ADAPTER_RABBITMQ_QUEUE_EXCLUSIVE', false),
                        'auto_delete' => env('QUERY_ADAPTER_RABBITMQ_QUEUE_AUTO_DEL', false),
                        'nowait' => env('QUERY_ADAPTER_RABBITMQ_QUEUE_NOWAIT', false),
                        'declare_properties' => [], // queue_declare properties/arguments
                        'bind_properties' => [], // queue_bind properties/arguments
                    ],

                    'consumer' => [
                        'tag' => env('QUERY_ADAPTER_RABBITMQ_CONSUMER_TAG', ''),
                        'no_local' => env('QUERY_ADAPTER_RABBITMQ_CONSUMER_NO_LOCAL', false),
                        'no_ack' => env('QUERY_ADAPTER_RABBITMQ_CONSUMER_NO_ACK', false),
                        'exclusive' => env('QUERY_ADAPTER_RABBITMQ_CONSUMER_EXCLUSIVE', false),
                        'nowait' => env('QUERY_ADAPTER_RABBITMQ_CONSUMER_NOWAIT', false),
                        'consumer_sleep_ms' => env('QUERY_ADAPTER_RABBITMQ_CONSUMER_SLEEP_MS', 1000),
                        'ticket' => null,
                        'properties' => [],
                    ],

                    'qos' => [
                        'enabled' => env('QUERY_ADAPTER_RABBITMQ_QOS_ENABLED', false),
                        'qos_prefetch_size' => env('QUERY_ADAPTER_RABBITMQ_QOS_PREF_SIZE', 0),
                        'qos_prefetch_count' => env('QUERY_ADAPTER_RABBITMQ_QOS_PREF_COUNT', 1),
                        'qos_a_global' => env('QUERY_ADAPTER_RABBITMQ_QOS_GLOBAL', false),
                    ],
                ],

                /*
                 * Set to "horizon" if you wish to use Laravel Horizon.
                 */
                'worker' => env('QUERY_ADAPTER_RABBITMQ_WORKER', 'default'),
                'after_commit' => false,

            ],
        ],

        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'retry_after' => 90,
            'after_commit' => false,
        ],
    ],


    /*
    |--------------------------------------------------------------------------
    | QueryAdapter engines list
    |--------------------------------------------------------------------------
    |
    | You can create your desired engine and place it in these sections, and after publishing, you can directly use the facade
    |
    */
    'paths' => [
        /*
        |--------------------------------------------------------------------------
        | Modules path
        |--------------------------------------------------------------------------
        |
        | This path is used to save the generated module.
        | This path will also be added automatically to the list of scanned folders.
        |
        */
        'modules' => base_path('Modules'),

        /*
        |--------------------------------------------------------------------------
        | Package engine base path
        |--------------------------------------------------------------------------
        |
        | Search engine entities are stored in this path.
        |
        */

        'base_path' => 'Engine/',
        /*
        |--------------------------------------------------------------------------
        | The app path
        |--------------------------------------------------------------------------
        |
        | app folder name
        | for example can change it to 'src' or 'App'
        */
        'app_folder' => 'app/',

        /*
        |--------------------------------------------------------------------------
        | The directives path
        |--------------------------------------------------------------------------
        |
        | directives folder name
        | for example can change it to 'src' or 'App'
        */
        'directives_folder' => 'Engine/Directives',

        /*
        |--------------------------------------------------------------------------
        | The directive prefix name
        |--------------------------------------------------------------------------
        |
        | for example can change it to '*'
        */
        'directive_prefix' => 'EngineDirective',

        /*
        |--------------------------------------------------------------------------
        | The facade path
        |--------------------------------------------------------------------------
        |
        | facade folder name
        | for example can change it to 'src' or 'App'
        */
        'facades_folder' => 'Engine/Facades',

        /*
        |--------------------------------------------------------------------------
        | The directive prefix name
        |--------------------------------------------------------------------------
        |
        | for example can change it to '*'
        */
        'facade_prefix' => 'EngineFacade',

        /*
        |--------------------------------------------------------------------------
        | The directives path
        |--------------------------------------------------------------------------
        |
        | directives folder name
        | for example can change it to 'src' or 'App'
        */
        'directives' => [
            // Directives classes path
        ],
    ],

    'builder' => [
        'directive' => ['path' => 'Engine/Directives', 'build' => false],
        'facade' => ['path' => 'Engine/Facades', 'build' => false],
    ],

    /*
    |--------------------------------------------------------------------------
    | QueryAdapter Stubs
    |--------------------------------------------------------------------------
    |
    | Default query_adapter stubs.
    |
    */
    'stubs' => [
        'enabled' => true,
        'path' => base_path('vendor/opsource/queryadapter/Support/stubs'),
        'files' => [
            'directives/engine-directive' => 'engine-directive.php',
        ],
        'replacements' => [
            'directives/engine-directive' => ['NAMESPACE', 'MODEL_NAMESPACE', 'DIRECTIVE_NAME', 'MODEL_NAME', 'INDICATOR', 'MODEL_INSTANCE'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | QueryAdapter commands
    |--------------------------------------------------------------------------
    |
    | In this section, the default commands of the package are located.
    | You can add your own commands to this section.
    |
    */
    'commands' => Opsource\QueryAdapter\CommandsServiceProvider::defaultCommands()
        ->merge([
            // New commands go here
        ])->toArray(),

    'directives' => [
    ],

    'facades' => [
    ],
];
