<?php namespace StudioBonito\SilverStripe\Queue;

/**
 * QueueManager.
 *
 * @author       Tom Densham <tom.densham@studiobonito.co.uk>
 * @copyright    Studio Bonito Ltd.
 * @package      StudioBonito\SilverStripe\Queue
 */
class QueueManager extends \Object
{
    protected $connectors = array();

    /**
     * The array of resolved queue connections.
     *
     * @var array
     */
    protected $connections = array();

    public function __construct(array $connectors)
    {
        $this->connectors = $connectors;
    }

    public function pop($connectionName, $queue = 'default', $delay = 0)
    {
        $connection = $this->connection($connectionName);

        $job = $this->getNextJob($connection, $queue);

        if (!is_null($job)) {

            try {
                $job->run();

                $job->delete();

                return array('job' => $job, 'failed' => false);
            } catch (\Exception $exception) {
                // TODO: Manage releasing the job if it's not deleted
                throw $exception;
            }
        } else {
            sleep($delay);

            return array('job' => null, 'failed' => false);
        }
    }

    /**
     * Get the next job from the queue connection.
     *
     * @param  \StudioBonito\SilverStripe\Queue\Queue $connection
     * @param  string                                 $queue
     *
     * @return \StudioBonito\SilverStripe\Queue\Jobs\JobInterface|null
     */
    protected function getNextJob($connection, $queue)
    {
        if (is_null($queue)) return $connection->pop();

        foreach (explode(',', $queue) as $queue) {
            if (!is_null($job = $connection->pop($queue))) return $job;
        }
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
        $config = $this->config()->get($name);

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
            return $this->connectors[$driver];
        }

        throw new \InvalidArgumentException("No connector for [$driver]");
    }

    /**
     * Get the name of the default queue connection.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->config()->get('default');
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
