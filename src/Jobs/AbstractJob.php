<?php namespace StudioBonito\SilverStripe\Queue\Jobs;

/**
 * AbstractJob.
 *
 * @author       Tom Densham <tom.densham@studiobonito.co.uk>
 * @copyright    Studio Bonito Ltd.
 * @package      StudioBonito\SilverStripe\Queue\Jobs
 */
abstract class AbstractJob
{
    /**
     * @var mixed
     */
    protected $instance;

    /**
     * @var string
     */
    protected $queue;

    abstract public function run();

    protected function resolveAndRun(array $payload)
    {
        list($class, $method) = $this->parseJob($payload['job']);

        $this->instance = $this->resolve($class);

        $this->instance->{$method}($this, $payload['data']);
    }

    protected function resolve($class)
    {
        return \Injector::inst()->get($class);
    }

    protected function parseJob($job)
    {
        $segments = explode('@', $job);

        return count($segments) > 1 ? $segments : array($segments[0], 'run');
    }

    /**
     * @return string
     */
    public function getQueue()
    {
        return $this->queue;
    }
}
