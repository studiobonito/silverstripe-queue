<?php namespace StudioBonito\SilverStripe\Queue;

use Pheanstalk_Job;
use Pheanstalk_Pheanstalk as Pheanstalk;
use StudioBonito\SilverStripe\Queue\Jobs\BeanstalkdJob;

/**
 * BeanstalkdQueue.
 *
 * @author       Tom Densham <tom.densham@studiobonito.co.uk>
 * @copyright    Studio Bonito Ltd.
 * @package      StudioBonito\SilverStripe\Queue
 */
class BeanstalkdQueue extends AbstractQueue implements QueueInterface
{
    /**
     * The Pheanstalk instance.
     *
     * @var Pheanstalk
     */
    protected $pheanstalk;

    /**
     * The name of the default tube.
     *
     * @var string
     */
    protected $default;

    /**
     * The "time to run" for all pushed jobs.
     *
     * @var int
     */
    protected $timeToRun;

    /**
     * Create a new Beanstalkd queue instance.
     *
     * @param  Pheanstalk $pheanstalk
     * @param  string     $default
     * @param  int        $timeToRun
     *
     * @return void
     */
    public function __construct(Pheanstalk $pheanstalk, $default, $timeToRun)
    {
        $this->default = $default;
        $this->timeToRun = $timeToRun;
        $this->pheanstalk = $pheanstalk;
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
    public function push($job, $data = null, $queue = 'default')
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
        return $this->pheanstalk->useTube($this->getQueue($queue))->put(
            $payload,
            Pheanstalk::DEFAULT_PRIORITY,
            Pheanstalk::DEFAULT_DELAY,
            $this->timeToRun
        );
    }

    public function pop($queue = 'default')
    {
        $queue = $this->getQueue($queue);

        $job = $this->pheanstalk->watchOnly($queue)->reserve(0);

        if ($job instanceof Pheanstalk_Job) {
            return new BeanstalkdJob($this->pheanstalk, $job, $queue);
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