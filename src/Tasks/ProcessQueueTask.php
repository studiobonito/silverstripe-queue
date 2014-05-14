<?php namespace StudioBonito\SilverStripe\Queue\Tasks;

use StudioBonito\SilverStripe\Queue\QueueManager;

/**
 * ProcessQueueTask.
 *
 * @author       Tom Densham <tom.densham@studiobonito.co.uk>
 * @copyright    Studio Bonito Ltd.
 * @package      StudioBonito\SilverStripe\Queue\Task
 */
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
     * QueueManager instance.
     *
     * @var QueueManager
     */
    protected $manager;

    /**
     * Ensure that the QueueManager instance gets injected.
     *
     * @param QueueManager $manager
     */
    public function __construct(QueueManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Implement this method in the task subclass to
     * execute via the TaskRunner
     */
    public function run($request)
    {
        $connection = $request->requestVar('connection') ? : null;

        $queue = $request->requestVar('queue') ? : 'default';

        $delay = $request->requestVar('delay') ? : 3;

        $this->manager->pop($connection, $queue, $delay);
    }
}
