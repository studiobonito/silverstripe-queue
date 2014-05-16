<?php namespace StudioBonito\SilverStripe\Queue\Connectors;

use StudioBonito\SilverStripe\Queue\DbQueue;

/**
 * DbConnector.
 *
 * @author       Tom Densham <tom.densham@studiobonito.co.uk>
 * @copyright    Studio Bonito Ltd.
 * @package      StudioBonito\SilverStripe\Queue\Connectors
 */
class DbConnector implements ConnectorInterface
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
        $queue = isset($config['queue']) ? $config['queue'] : 'default';

        return DbQueue::create($queue);
    }
} 