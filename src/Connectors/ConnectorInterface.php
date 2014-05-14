<?php namespace StudioBonito\SilverStripe\Queue\Connectors;

/**
 * ConnectorInterface.
 *
 * @author       Tom Densham <tom.densham@studiobonito.co.uk>
 * @copyright    Studio Bonito Ltd.
 * @package      StudioBonito\SilverStripe\Queue\Connectors
 */
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