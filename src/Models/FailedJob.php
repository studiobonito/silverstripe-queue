<?php namespace StudioBonito\SilverStripe\Queue\Models;

/**
 * FailedJob.
 *
 * @author       Tom Densham <tom.densham@studiobonito.co.uk>
 * @copyright    Studio Bonito Ltd.
 */
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