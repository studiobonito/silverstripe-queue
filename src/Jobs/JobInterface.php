<?php namespace StudioBonito\SilverStripe\Queue\Jobs;

/**
 * JobInterface.
 *
 * @author       Tom Densham <tom.densham@studiobonito.co.uk>
 * @copyright    Studio Bonito Ltd.
 * @package      StudioBonito\SilverStripe\Queue\Jobs
 */
interface JobInterface
{
    /**
     * Fire the job.
     *
     * @return void
     */
    public function run();

    /**
     * Delete the job from the queue.
     *
     * @return void
     */
    public function delete();
}