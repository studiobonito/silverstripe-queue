<?php namespace StudioBonito\SilverStripe\Queue\Models;

/**
 * @property int    ID
 * @property string ClassName
 * @property string Created
 * @property string LastEdited
 * @property string Queue
 * @property string Payload
 * @property int    RunAfter
 */
class JobQueue extends \DataObject
{
    /**
     * List of database fields. {@link DataObject::$db}
     *
     * @var array
     */
    private static $db = array(
        'Queue'    => 'Varchar(255)',
        'Payload'  => 'Text',
        'RunAfter' => 'Int'
    );
}
