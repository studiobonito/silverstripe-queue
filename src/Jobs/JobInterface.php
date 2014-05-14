<?php namespace StudioBonito\SilverStripe\Queue\Jobs;

/**
 * JobInterface.
 *
 * @author       Tom Densham <tom.densham@studiobonito.co.uk>
 * @copyright    Studio Bonito Ltd.
 * @package      StudioBonito\SilverStripe\Queue\Jobs
 */
interface JobInterface
{
    public function run();

    public function delete();
}