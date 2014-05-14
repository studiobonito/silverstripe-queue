<?php namespace StudioBonito\SilverStripe\Queue;

/**
 * AbstractQueue.
 *
 * @author       Tom Densham <tom.densham@studiobonito.co.uk>
 * @copyright    Studio Bonito Ltd.
 * @package      StudioBonito\SilverStripe\Queue
 */
abstract class AbstractQueue extends \Object
{
    protected function createPayload($job, $data = null)
    {
        return \Convert::raw2json(array('job' => $job, 'data' => $data));
    }
}
