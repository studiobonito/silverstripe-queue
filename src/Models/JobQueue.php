<?php namespace StudioBonito\SilverStripe\Queue\Models;

/**
 * JobQueue.
 *
 * @author       Tom Densham <tom.densham@studiobonito.co.uk>
 * @copyright    Studio Bonito Ltd.
 * @package      StudioBonito\SilverStripe\Queue\Model
 */
class JobQueue extends \DataObject
{
    /**
     * List of database fields. {@link DataObject::$db}
     *
     * @var array
     */
    private static $db = array(
        'Queue'   => 'Varchar(255)',
        'Payload' => 'Text',
    );
}
