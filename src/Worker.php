<?php namespace StudioBonito\SilverStripe\Queue;

use StudioBonito\SilverStripe\Queue\Failed\FailedJobProviderInterface;
use StudioBonito\SilverStripe\Queue\Jobs\AbstractJob;

class Worker
{
    /**
     * THe queue manager instance.
     *
     * @var \StudioBonito\SilverStripe\Queue\QueueManager
     */
    protected $manager;

    /**
     * The failed job provider implementation.
     *
     * @var \StudioBonito\SilverStripe\Queue\Failed\FailedJobProviderInterface
     */
    protected $failer;

    /**
     * Flag used when running as a daemon.
     *
     * @var bool
     */
    protected $stop = false;

    /**
     * Create a new queue worker.
     *
     * @param  \StudioBonito\SilverStripe\Queue\QueueManager $manager
     *
     * @return void
     */
    public function __construct(QueueManager $manager, FailedJobProviderInterface $failer = null)
    {
        $this->manager = $manager;
        $this->failer = $failer;
    }

    /**
     * Listen to the given queue in a loop.
     *
     * @param  string $connectionName
     * @param  string $queue
     * @param  int    $delay
     * @param  int    $memory
     * @param  int    $sleep
     * @param  int    $maxTries
     *
     * @return array
     */
    public function daemon($connectionName, $queue = null, $delay = 0, $memory = 128, $sleep = 3, $maxTries = 0)
    {
        while (true) {
            if (!$this->stop) {
                $this->runNextJobForDaemon($connectionName, $queue, $delay, $sleep, $maxTries);
            } else {
                $this->sleep($sleep);
            }

            if ($this->memoryExceeded($memory)) {
                $this->stop();
            }
        }
    }

    /**
     * Run the next job for the daemon worker.
     *
     * @param  string $connectionName
     * @param  string $queue
     * @param  int    $delay
     * @param  int    $sleep
     * @param  int    $maxTries
     *
     * @return void
     */
    protected function runNextJobForDaemon($connectionName, $queue, $delay, $sleep, $maxTries)
    {
        $this->pop($connectionName, $queue, $delay, $sleep, $maxTries);
    }

    /**
     * Listen to the given queue.
     *
     * @param  string $connectionName
     * @param  string $queue
     * @param  int    $delay
     * @param  int    $memory
     * @param  int    $sleep
     * @param  int    $maxTries
     *
     * @return void
     */
    public function pop($connectionName, $queue = null, $delay = 0, $memory = 128, $sleep = 3, $maxTries = 0)
    {
        $connection = $this->manager->connection($connectionName);

        $job = $this->getNextJob($connection, $queue);

        if (!is_null($job)) {
            $this->process($this->manager->getName($connectionName), $job, $maxTries, $delay);
        } else {
            $this->sleep($sleep);
        }
    }

    /**
     * Get the next job from the queue connection.
     *
     * @param  \StudioBonito\SilverStripe\Queue\QueueInterface $connection
     * @param  string                                          $queue
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
     * Process a given job from the queue.
     *
     * @param  string                                            $connection
     * @param  \StudioBonito\SilverStripe\Queue\Jobs\AbstractJob $job
     * @param  int                                               $maxTries
     * @param  int                                               $delay
     *
     * @return void
     *
     * @throws \Exception
     */
    public function process($connection, AbstractJob $job, $maxTries = 0, $delay = 0)
    {
        if ($maxTries > 0 && $job->attempts() > $maxTries) {
            $this->logFailedJob($connection, $job);

            return;
        }

        try {
            // First we will fire off the job. Once it is done we will see if it will
            // be auto-deleted after processing and if so we will go ahead and run
            // the delete method on the job. Otherwise we will just keep moving.
            $job->run();

            if ($job->autoDelete()) $job->delete();
        } catch (\Exception $exception) {
            // If we catch an exception, we will attempt to release the job back onto
            // the queue so it is not lost. This will let is be retried at a later
            // time by another listener (or the same one). We will do that here.
            if (!$job->isDeleted()) $job->release($delay);
            throw $exception;
        }
    }

    /**
     * Log a failed job into storage.
     *
     * @param  string                                            $connection
     * @param  \StudioBonito\SilverStripe\Queue\Jobs\AbstractJob $job
     *
     * @return void
     */
    protected function logFailedJob($connection, AbstractJob $job)
    {
        if ($this->failer) {
            $this->failer->log($connection, $job->getQueue(), $job->getRawPayload());

            $job->delete();
        }
    }

    /**
     * Determine if the memory limit has been exceeded.
     *
     * @param  int $memoryLimit
     *
     * @return bool
     */
    public function memoryExceeded($memoryLimit)
    {
        return (memory_get_usage() / 1024 / 1024) >= $memoryLimit;
    }

    /**
     * Stop listening and bail out of the script.
     *
     * @return void
     */
    public function stop()
    {
        $this->stop = true;

        die;
    }

    /**
     * Sleep the script for a given number of seconds.
     *
     * @param  int $seconds
     *
     * @return void
     */
    public function sleep($seconds)
    {
        usleep($seconds * 1000000);
    }
} 