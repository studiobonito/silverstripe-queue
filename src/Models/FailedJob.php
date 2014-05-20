<?php namespace StudioBonito\SilverStripe\Queue\Models;

class FailedJob extends \DataObject
{
    /**
     * List of database fields. {@link DataObject::$db}
     *
     * @var array
     */
    private static $db = array(
        'Queue'   => 'Varchar(255)',
        'Payload' => 'Text',
        'Failed'  => 'Datetime'
    );
}