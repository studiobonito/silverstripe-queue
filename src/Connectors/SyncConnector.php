<?php namespace StudioBonito\SilverStripe\Queue\Connectors;

use StudioBonito\SilverStripe\Queue\SyncQueue;

class SyncConnector implements ConnectorInterface
{
    /**
     * Establish a queue connection.
     *
     * @param  array $config
     *
     * @return \StudioBonito\SilverStripe\Queue\QueueInterface
     */
    public function connect(array $config)
    {
        return new SyncQueue;
    }
} 