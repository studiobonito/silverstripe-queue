<?php namespace StudioBonito\SilverStripe\Queue\Connectors;

use Pheanstalk_Pheanstalk as Pheanstalk;
use StudioBonito\SilverStripe\Queue\BeanstalkdQueue;

/**
 * BeanstalkdConnector.
 *
 * @author       Tom Densham <tom.densham@studiobonito.co.uk>
 * @copyright    Studio Bonito Ltd.
 * @package      StudioBonito\SilverStripe\Queue\Connectors
 */
class BeanstalkdConnector implements ConnectorInterface
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
        $pheanstalk = new Pheanstalk($config['host']);

        $queue = isset($config['queue']) ? $config['queue'] : 'default';

        $timeToRun = isset($config['ttr']) ? $config['ttr'] : Pheanstalk::DEFAULT_TTR;

        return new BeanstalkdQueue($pheanstalk, $queue, $timeToRun);
    }
}