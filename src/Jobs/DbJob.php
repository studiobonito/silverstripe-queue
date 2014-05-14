<?php namespace StudioBonito\SilverStripe\Queue\Jobs;

use StudioBonito\SilverStripe\Queue\Models\JobQueue;

/**
 * DbJob.
 *
 * @author       Tom Densham <tom.densham@studiobonito.co.uk>
 * @copyright    Studio Bonito Ltd.
 * @package      StudioBonito\SilverStripe\Queue\Jobs
 */
class DbJob extends AbstractJob implements JobInterface
{
    /**
     * @var JobQueue
     */
    protected $job;

    function __construct(JobQueue $job, $queue)
    {
        $this->job = $job;
        $this->queue = $queue;
    }

    public function run()
    {
        $this->resolveAndRun(\Convert::json2array($this->getRawPayload()));
    }

    public function delete()
    {
        $this->job->delete();
    }

    public function getRawPayload()
    {
        return $this->job->Payload;
    }
}
 