<?php namespace StudioBonito\SilverStripe\Queue\Failed;

use StudioBonito\SilverStripe\Queue\Models\FailedJob;

/**
 * DbFailedJobProvider.
 *
 * @author       Tom Densham <tom.densham@studiobonito.co.uk>
 * @copyright    Studio Bonito Ltd.
 */
class DbFailedJobProvider implements FailedJobProviderInterface
{
    /**
     * Log a failed job into storage.
     *
     * @param  string $connection
     * @param  string $queue
     * @param  string $payload
     *
     * @return void
     */
    public function log($connection, $queue, $payload)
    {
        $failed = \SS_Datetime::now();

        FailedJob::create(array('Queue' => $queue, 'Payload' => $payload, 'Failed' => $failed))->write();
    }

    /**
     * Get a list of all of the failed jobs.
     *
     * @return array
     */
    public function all()
    {
        return FailedJob::get()->sort('ID DESC')->toArray();
    }

    /**
     * Get a single failed job.
     *
     * @param  mixed $id
     *
     * @return array
     */
    public function find($id)
    {
        return FailedJob::get()->filter('ID', $id)->first()->toArray();
    }

    /**
     * Delete a single failed job from storage.
     *
     * @param  mixed $id
     *
     * @return bool
     */
    public function forget($id)
    {
        return FailedJob::get()->filter('ID', $id)->removeAll()->count() == 0;
    }

    /**
     * Flush all of the failed jobs from storage.
     *
     * @return void
     */
    public function flush()
    {
        FailedJob::get()->removeAll();
    }
}