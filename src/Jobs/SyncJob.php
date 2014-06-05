<?php namespace StudioBonito\SilverStripe\Queue\Jobs;

use Closure;
use Injector;

class SyncJob extends AbstractJob
{
    /**
     * The class name of the job.
     *
     * @var string
     */
    protected $job;

    /**
     * The queue message data.
     *
     * @var string
     */
    protected $data;

    /**
     * Create a new job instance.
     *
     * @param  \Injector $injector
     * @param  string    $job
     * @param  string    $data
     *
     * @return void
     */
    public function __construct(Injector $injector, $job, $data = '')
    {
        $this->job = $job;
        $this->data = $data;
        $this->injector = $injector;
    }

    /**
     * Run the job.
     *
     * @return void
     */
    public function run()
    {
        $data = json_decode($this->data, true);

        if ($this->job instanceof Closure) {
            call_user_func($this->job, $this, $data);
        } else {
            $this->resolveAndRun(array('job' => $this->job, 'data' => $data));
        }
    }

    /**
     * Get the raw body string for the job.
     *
     * @return string
     */
    public function getRawPayload()
    {
        //
    }

    /**
     * Delete the job from the queue.
     *
     * @return void
     */
    public function delete()
    {
        parent::delete();
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
        //
    }

    /**
     * Get the number of times the job has been attempted.
     *
     * @return int
     */
    public function attempts()
    {
        return 1;
    }

    /**
     * Get the job identifier.
     *
     * @return string
     */
    public function getJobId()
    {
        return '';
    }
} 