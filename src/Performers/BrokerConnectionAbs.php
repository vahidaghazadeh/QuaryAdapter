<?php

namespace packages\Opsource\QueryAdapter\src\Performers;

use InvalidArgumentException;
use PhpAmqpLib\Wire\IO\AbstractIO;

abstract class BrokerConnectionAbs
{
    public static $LIBRARY_PROPERTIES = array();


//    public static $LIBRARY_PROPERTIES = array(
//        'product' => array('S', Package::NAME),
//        'platform' => array('S', 'PHP'),
//        'version' => array('S', Package::VERSION),
//        'information' => array('S', ''),
//        'copyright' => array('S', ''),
//        'capabilities' => array(
//            'F',
//            array(
//                'authentication_failure_close' => array('t', true),
//                'publisher_confirms' => array('t', true),
//                'consumer_cancel_notify' => array('t', true),
//                'exchange_exchange_bindings' => array('t', true),
//                'basic.nack' => array('t', true),
//                'connection.blocked' => array('t', true)
//            )
//        )
//    );

    public $channels = array();

    /** @var int */
    protected $version_major;

    /** @var int */
    protected $version_minor;

    /** @var array */
    protected $server_properties;

    /** @var array */
    protected $mechanisms;

    /** @var array */
    protected $locales;

    /** @var bool */
    protected $wait_tune_ok;

    /** @var string */
    protected $known_hosts;

    protected $input;

    /** @var string */
    protected $vhost;

    /** @var bool */
    protected $insist;

    /** @var string */
    protected $login_method;

    /**
     * @var null|string
     */
    protected $login_response;

    /** @var string */
    protected $locale;

    /** @var int */
    protected $heartbeat;

    /** @var float */
    protected $last_frame;

    /** @var int */
    protected $channel_max = 65535;

    /** @var int */
    protected $frame_max = 131072;

    /** @var array Constructor parameters for clone */
    protected $construct_params;

    /** @var bool Close the connection in destructor */
    protected $close_on_destruct = true;

    /** @var bool Maintain connection status */
    protected $is_connected = false;

    /** @var AbstractIO */
    protected $io;

    /** @var callable Handles connection blocking from the server */
    private $connection_block_handler;

    /** @var callable Handles connection unblocking from the server */
    private $connection_unblock_handler;

    /** @var int Connection timeout value*/
    protected $connection_timeout;

    /** @var null */
    protected $config;

    /**
     * Circular buffer to speed up prepare_content().
     * Max size limited by $prepare_content_cache_max_size.
     *
     * @var array
     * @see prepare_content()
     */
    private $prepare_content_cache = array();

    /** @var int Maximal size of $prepare_content_cache */
    private $prepare_content_cache_max_size = 100;

    /**
     * Maximum time to wait for channel operations, in seconds
     * @var float $channel_rpc_timeout
     */
    private $channel_rpc_timeout;

    /**
     * If connection is blocked due to the broker running low on resources.
     * @var bool
     */
    protected $blocked = false;

    /**
     * If a frame is currently being written
     * @var bool
     */
    protected $writing = false;

    public function __construct(
        $user,
        $password,
        $vhost = '/',
        $insist = false,
        $login_method = 'AMQPLAIN',
        $login_response = null,
        $locale = 'en_US',
        AbstractIO $io = null,
        $heartbeat = 0,
        $connection_timeout = 0,
        $channel_rpc_timeout = 0.0,
        $config = null
    ) {
        if (is_null($io)) {
            throw new InvalidArgumentException('Argument $io cannot be null');
        }

        if ($config) {
            $this->config = clone $config;
        }

        // save the params for the use of __clone
        $this->construct_params = func_get_args();

        $this->vhost = $vhost;
        $this->insist = $insist;
        $this->login_method = $login_method;
        $this->locale = $locale;
        $this->io = $io;
        $this->heartbeat = max(0, (int)$heartbeat);
        $this->connection_timeout = $connection_timeout;
        $this->channel_rpc_timeout = $channel_rpc_timeout;

        if ($user && $password) {
            if ($login_method === 'PLAIN') {
                $this->login_response = sprintf("\0%s\0%s", $user, $password);
            } elseif ($login_method === 'AMQPLAIN') {
                $login_response = new AMQPWriter();
                $login_response->write_table(array(
                    'LOGIN' => array('S', $user),
                    'PASSWORD' => array('S', $password)
                ));

                // Skip the length
                $responseValue = $login_response->getvalue();
                $this->login_response = mb_substr($responseValue, 4, mb_strlen($responseValue, 'ASCII') - 4, 'ASCII');
            } else {
                throw new \InvalidArgumentException('Unknown login method: ' . $login_method);
            }
        } elseif ($login_method === 'EXTERNAL') {
            $this->login_response = $login_response;
        } else {
            $this->login_response = null;
        }

        // Lazy Connection waits on connecting
        if ($this->connectOnConstruct()) {
            $this->connect();
        }
    }
}
