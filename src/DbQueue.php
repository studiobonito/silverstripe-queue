<?php namespace StudioBonito\SilverStripe\Queue;

use StudioBonito\SilverStripe\Queue\Jobs\DbJob;
use StudioBonito\SilverStripe\Queue\Models\JobQueue;

/**
 * DbQueue.
 *
 * @author       Tom Densham <tom.densham@studiobonito.co.uk>
 * @copyright    Studio Bonito Ltd.
 * @package      StudioBonito\SilverStripe\Queue
 */
class DbQueue extends AbstractQueue implements QueueInterface
{
    /**
     * The name of the default tube.
     *
     * @var string
     */
    protected $default;

    /**
     * Create a new SilverStripe ORM queue instance.
     *
     * @param  string $default
     *
     * @return void
     */
    public function __construct($default)
    {
        $this->default = $default;
    }

    public function push($job, $data = null, $queue = null)
    {
        $payload = $this->createPayload($job, $data);

        JobQueue::create(array('Queue' => $queue, 'Payload' => $payload))->write();
    }

    public function pop($queue = null)
    {
        $job = JobQueue::get()->filter('Queue', $queue)->first();

        if ($job instanceof JobQueue) {
            return new DbJob($job, $queue);
        }
    }

    /**
     * Get the queue or return the default.
     *
     * @param  string|null $queue
     *
     * @return string
     */
    public function getQueue($queue)
    {
        return $queue ? : $this->default;
    }
}
