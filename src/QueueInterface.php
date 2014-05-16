<?php namespace StudioBonito\SilverStripe\Queue;

/**
 * QueueInterface.
 *
 * @author       Tom Densham <tom.densham@studiobonito.co.uk>
 * @copyright    Studio Bonito Ltd.
 * @package      StudioBonito\SilverStripe\Queue
 */
interface QueueInterface
{
    public function push($job, $data = null, $queue = null);

    public function pop($queue = null);
}