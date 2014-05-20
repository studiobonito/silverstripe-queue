<?php namespace StudioBonito\SilverStripe\Queue\Jobs;

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