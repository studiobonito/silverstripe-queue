<?php namespace StudioBonito\SilverStripe\Queue\Jobs;

use DateTime;

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

    /**
     * Indicates if the job has been deleted.
     *
     * @var bool
     */
    protected $deleted = false;

    /**
     * Run the job.
     *
     * @return void
     */
    abstract public function run();

    /**
     * Delete the job from the queue.
     *
     * @return void
     */
    public function delete()
    {
        $this->deleted = true;
    }

    /**
     * Determine if the job has been deleted.
     *
     * @return bool
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Release the job back into the queue.
     *
     * @param  int $delay
     *
     * @return void
     */
    abstract public function release($delay = 0);

    /**
     * Get the number of times the job has been attempted.
     *
     * @return int
     */
    abstract public function attempts();

    /**
     * Get the raw payload string for the job.
     *
     * @return string
     */
    abstract public function getRawPayload();

    /**
     * Resolve and fire the job handler method.
     *
     * @param  array $payload
     *
     * @return void
     */
    protected function resolveAndRun(array $payload)
    {
        list($class, $method) = $this->parseJob($payload['job']);

        $this->instance = $this->resolve($class);

        $this->instance->{$method}($this, $payload['data']);
    }

    /**
     * Resolve the given job handler.
     *
     * @param  string $class
     *
     * @return mixed
     */
    protected function resolve($class)
    {
        return \Injector::inst()->get($class);
    }

    /**
     * Parse the job declaration into class and method.
     *
     * @param  string $job
     *
     * @return array
     */
    protected function parseJob($job)
    {
        $segments = explode('@', $job);

        return count($segments) > 1 ? $segments : array($segments[0], 'run');
    }

    /**
     * Determine if job should be auto-deleted.
     *
     * @return bool
     */
    public function autoDelete()
    {
        return isset($this->instance->delete);
    }

    /**
     * Calculate the number of seconds with the given delay.
     *
     * @param  \DateTime|int $delay
     *
     * @return int
     */
    protected function getSeconds($delay)
    {
        if ($delay instanceof DateTime) {
            return max(0, $delay->getTimestamp() - $this->getTime());
        } else {
            return intval($delay);
        }
    }

    /**
     * Get the name of the queue the job belongs to.
     *
     * @return string
     */
    public function getQueue()
    {
        return $this->queue;
    }
}
