<?php namespace StudioBonito\SilverStripe\Queue\Connectors;

interface ConnectorInterface
{
    /**
     * Establish a queue connection.
     *
     * @param  array $config
     *
     * @return \StudioBonito\SilverStripe\Queue\QueueInterface
     */
    public function connect(array $config);
}