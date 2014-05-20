<?php namespace StudioBonito\SilverStripe\Queue\Jobs;

use Pheanstalk_Job;
use Pheanstalk_Pheanstalk as Pheanstalk;

/**
 * BeanstalkdJob.
 *
 * @author       Tom Densham <tom.densham@studiobonito.co.uk>
 * @copyright    Studio Bonito Ltd.
 * @package      StudioBonito\SilverStripe\Queue\Jobs
 */
class BeanstalkdJob extends AbstractJob implements JobInterface
{
    /**
     * The Pheanstalk instance.
     *
     * @var Pheanstalk
     */
    protected $pheanstalk;

    /**
     * The Pheanstalk job instance.
     *
     * @var Pheanstalk_Job
     */
    protected $job;

    /**
     * Create a new job instance.
     *
     * @param  Pheanstalk     $pheanstalk
     * @param  Pheanstalk_Job $job
     * @param  string         $queue
     *
     * @return void
     */
    public function __construct(Pheanstalk $pheanstalk, Pheanstalk_Job $job, $queue)
    {
        $this->job = $job;
        $this->queue = $queue;
        $this->pheanstalk = $pheanstalk;
    }

    /**
     * Run the job.
     *
     * @return void
     */
    public function run()
    {
        $this->resolveAndRun(\Convert::json2array($this->getRawPayload()));
    }

    /**
     * Get the raw payload string for the job.
     *
     * @return string
     */
    public function getRawPayload()
    {
        return $this->job->getData();
    }

    /**
     * Delete the job from the queue.
     *
     * @return void
     */
    public function delete()
    {
        parent::delete();

        $this->pheanstalk->delete($this->job);
    }

    /**
     * Release the job back into the queue.
     *
     * @param  int $delay
     *
     * @return void
     */
    public function release($delay = 0)
    {
        $priority = Pheanstalk::DEFAULT_PRIORITY;

        $this->pheanstalk->release($this->job, $priority, $delay);
    }

    /**
     * Bury the job in the queue.
     *
     * @return void
     */
    public function bury()
    {
        $this->pheanstalk->bury($this->job);
    }

    /**
     * Get the number of times the job has been attempted.
     *
     * @return int
     */
    public function attempts()
    {
        $stats = $this->pheanstalk->statsJob($this->job);

        return (int)$stats->reserves;
    }

    /**
     * Get the job identifier.
     *
     * @return string
     */
    public function getJobId()
    {
        return $this->job->getId();
    }

    /**
     * Get the underlying Pheanstalk instance.
     *
     * @return Pheanstalk
     */
    public function getPheanstalk()
    {
        return $this->pheanstalk;
    }

    /**
     * Get the underlying Pheanstalk job.
     *
     * @return Pheanstalk_Job
     */
    public function getPheanstalkJob()
    {
        return $this->job;
    }
}
 