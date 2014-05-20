<?php namespace StudioBonito\SilverStripe\Queue\Tasks;

use StudioBonito\SilverStripe\Queue\QueueManager;

class ProcessQueueTask extends \BuildTask
{
    /**
     * Task title used in web interface.
     *
     * @var string
     */
    protected $title = 'Process task queue';

    /**
     * @var string $description Describe the implications the task has,
     * and the changes it makes. Accepts HTML formatting.
     */
    protected $description = 'Process the next job on a queue.';

    /**
     * The queue worker instance.
     *
     * @var \StudioBonito\SilverStripe\Queue\Worker
     */
    protected $worker;

    /**
     * Ensure that the QueueManager instance gets injected.
     *
     * @param \StudioBonito\SilverStripe\Queue\Worker $worker
     */
    public function __construct(QueueManager $worker)
    {
        $this->worker = $worker;
    }

    /**
     * Implement this method in the task subclass to
     * execute via the TaskRunner
     */
    public function run($request)
    {
        $connection = $request->getVar('connection');

        $queue = $request->getVar('queue');

        $delay = $request->getVar('delay') ? : 0;

        $memory = $request->getVar('memory') ? : 128;

        $sleep = $request->getVar('sleep') ? : 3;

        $tries = $request->getVar('tries') ? : 0;

        $daemon = $request->getVar('daemon');

        if ($daemon) {
            return $this->worker->daemon($connection, $queue, $delay, $memory, $sleep, $tries);
        } else {
            return $this->worker->pop($connection, $queue, $delay, $sleep, $tries);
        }
    }
}
