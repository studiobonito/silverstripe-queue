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

    public function run()
    {
        $this->resolveAndRun(\Convert::json2array($this->getRawPayload()));
    }

    public function delete()
    {
        $this->pheanstalk->delete($this->job);
    }

    public function getRawPayload()
    {
        return $this->job->getData();
    }
}
 