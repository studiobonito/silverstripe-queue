<?php namespace StudioBonito\SilverStripe\Queue;

use StudioBonito\SilverStripe\Queue\Jobs\DbJob;
use StudioBonito\SilverStripe\Queue\Models\JobQueue;

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

    /**
     * Create a payload string from the given job and data.
     *
     * @param  string $job
     * @param  mixed  $data
     *
     * @return string
     */
    protected function createPayload($job, $data = null)
    {
        $payload = parent::createPayload($job, $data);

        return $this->setMeta($payload, 'attempts', 1);
    }

    /**
     * Push a new job onto the queue.
     *
     * @param  string $job
     * @param  mixed  $data
     * @param  string $queue
     *
     * @return mixed
     */
    public function push($job, $data = null, $queue = null)
    {
        return $this->pushRaw($this->createPayload($job, $data), $queue);
    }

    /**
     * Push a raw payload onto the queue.
     *
     * @param  string $payload
     * @param  string $queue
     * @param  array  $options
     *
     * @return mixed
     */
    public function pushRaw($payload, $queue = null, array $options = array())
    {
        return JobQueue::create(
            array('Queue' => $this->getQueue($queue), 'Payload' => $payload, 'RunAfter' => $this->getTime())
        )->write();
    }

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param  \DateTime|int $delay
     * @param  string        $job
     * @param  mixed         $data
     * @param  string        $queue
     *
     * @return mixed
     */
    public function later($delay, $job, $data = '', $queue = null)
    {
        $payload = $this->createPayload($job, $data);

        $delay = $this->getSeconds($delay);

        JobQueue::create(
            array('Queue' => $this->getQueue($queue), 'Payload' => $payload, 'RunAfter' => $this->getTime() + $delay)
        )->write();
    }

    /**
     * Release a reserved job back onto the queue.
     *
     * @param  string $queue
     * @param  string $payload
     * @param  string $delay
     * @param  int    $attempts
     *
     * @return void
     */
    public function release($queue, $payload, $delay, $attempts)
    {
        $payload = $this->setMeta($payload, 'attempts', $attempts);

        JobQueue::create(
            array('Queue' => $this->getQueue($queue), 'Payload' => $payload, 'RunAfter' => $this->getTime() + $delay)
        )->write();
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param  string $queue
     *
     * @return \StudioBonito\SilverStripe\Queue\Jobs\AbstractJob|null
     */
    public function pop($queue = null)
    {
        $queue = $this->getQueue($queue);

        $job = JobQueue::get()->filter(array('Queue' => $queue, 'RunAfter:LessThan' => $this->getTime()))->first();

        if ($job instanceof JobQueue) {
            return new DbJob($this, $job, $queue);
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
