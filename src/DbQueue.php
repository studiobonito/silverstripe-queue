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
    public function push($job, $data = null, $queue = 'default')
    {
        $payload = $this->createPayload($job, $data);

        JobQueue::create(array('Queue' => $queue, 'Payload' => $payload))->write();
    }

    public function pop($queue = 'default')
    {
        $job = JobQueue::get()->filter('Queue', $queue)->first();

        if ($job instanceof JobQueue) {
            return new DbJob($job, $queue);
        }
    }
}
