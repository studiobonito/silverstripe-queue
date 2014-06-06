<?php namespace StudioBonito\SilverStripe\Queue;

use Config;
use Closure;
use Injector;
use StudioBonito\SilverStripe\Queue\Connectors\ConnectorInterface;

class QueueManager
{
    /**
     * The config instance.
     *
     * @var \Config_ForClass
     */
    protected $config;

    /**
     * The array of resolved queue connections.
     *
     * @var array
     */
    protected $connectors = array();

    /**
     * The array of resolved queue connections.
     *
     * @var array
     */
    protected $connections = array();

    /**
     * Fetch the current instance of the queue manager.
     *
     * @return \StudioBonito\SilverStripe\Queue\QueueManager
     */
    public static function inst()
    {
        return Injector::inst()->get('StudioBonito\SilverStripe\Queue\QueueManager');
    }

    /**
     * Use DI to pass in the connectors
     *
     * @param array $connectors
     */
    public function __construct(array $connectors)
    {
        $this->connectors = $connectors;

        $this->setConfig(Config::inst()->forClass(get_called_class()));
    }

    /**
     * Determine if the driver is connected.
     *
     * @param  string  $name
     * @return bool
     */
    public function connected($name = null)
    {
        return isset($this->connections[$name ?: $this->getDefaultDriver()]);
    }

    /**
     * Resolve a queue connection instance.
     *
     * @param  string $name
     *
     * @return \StudioBonito\SilverStripe\Queue\QueueInterface
     */
    public function connection($name = null)
    {
        $name = $name ? : $this->getDefaultDriver();

        // If the connection has not been resolved yet we will resolve it now as all
        // of the connections are resolved when they are actually needed so we do
        // not make any unnecessary connection to the various queue end-points.
        if (!isset($this->connections[$name])) {
            $this->connections[$name] = $this->resolve($name);

            $this->connections[$name]->setInjector(Injector::inst());
        }

        return $this->connections[$name];
    }

    /**
     * Resolve a queue connection.
     *
     * @param  string $name
     *
     * @return \StudioBonito\SilverStripe\Queue\QueueInterface
     */
    protected function resolve($name)
    {
        $config = $this->getConfig()->get($name);

        return $this->getConnector($config['driver'])->connect($config);
    }

    /**
     * Get the connector for a given driver.
     *
     * @param  string $driver
     *
     * @return \StudioBonito\SilverStripe\Queue\Connectors\ConnectorInterface
     *
     * @throws \InvalidArgumentException
     */
    protected function getConnector($driver)
    {
        if (isset($this->connectors[$driver])) {
            if ($this->connectors[$driver] instanceof ConnectorInterface) {
                return $this->connectors[$driver];
            } else {
                return call_user_func($this->connectors[$driver]);
            }
        }

        throw new \InvalidArgumentException("No connector for [$driver]");
    }

    /**
     * Add a queue connection resolver.
     *
     * @param  string  $driver
     * @param  Closure $resolver
     *
     * @return void
     */
    public function addConnector($driver, Closure $resolver)
    {
        $this->connectors[$driver] = $resolver;
    }

    /**
     * Set the config instance.
     *
     * @param \Config_ForClass $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * Get the config instance.
     *
     * @return \Config_ForClass
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get the name of the default queue connection.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->getConfig()->get('default');
    }

    /**
     * Set the name of the default queue connection.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->getConfig()->set('default', $name);
    }

    /**
     * Get the full name for the given connection.
     *
     * @param  string  $connection
     * @return string
     */
    public function getName($connection = null)
    {
        return $connection ?: $this->getDefaultDriver();
    }

    /**
     * Dynamically pass calls to the default connection.
     *
     * @param  string $method
     * @param  array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $callable = array($this->connection(), $method);

        return call_user_func_array($callable, $parameters);
    }
}
