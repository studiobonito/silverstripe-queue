<?php namespace StudioBonito\SilverStripe\Queue;

/**
 * Worker.
 *
 * @author       Tom Densham <tom.densham@studiobonito.co.uk>
 * @copyright    Studio Bonito Ltd.
 * @package      StudioBonito\SilverStripe\Queue
 */
class Worker
{
    /**
     * THe queue manager instance.
     *
     * @var \StudioBonito\SilverStripe\Queue\QueueManager
     */
    protected $manager;

    /**
     * Create a new queue worker.
     *
     * @param  \StudioBonito\SilverStripe\Queue\QueueManager $manager
     *
     * @return void
     */
    public function __construct(QueueManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Listen to the given queue.
     *
     * @param  string $connectionName
     * @param  string $queue
     * @param  int    $delay
     *
     * @return void
     */
    public function pop($connectionName, $queue = 'default', $delay = 0)
    {
        $connection = $this->manager->connection($connectionName);

        $job = $this->getNextJob($connection, $queue);

        if (!is_null($job)) {

            try {
                $job->run();

                $job->delete();
            } catch (\Exception $exception) {
                // TODO: Manage releasing the job if it's not deleted
                throw $exception;
            }
        } else {
            sleep($delay);
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
} 