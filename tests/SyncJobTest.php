<?php namespace StudioBonito\SilverStripe\Queue\Tests;

use Injector;
use Mockery as m;

class SyncJobTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testRunResolvesAndRunsJobClass()
    {
        $injector = m::mock('Injector');
        $job = new \StudioBonito\SilverStripe\Queue\Jobs\SyncJob($injector, 'Foo', '"data"');
        $handler = m::mock('StdClass');
        $injector->shouldReceive('get')->once()->with('Foo')->andReturn($handler);
        $handler->shouldReceive('run')->once()->with($job, 'data');

        $job->run();
    }

    public function testClosuresCanBeRunBySyncJob()
    {
        unset($_SERVER['__queue.closure']);
        $job = new \StudioBonito\SilverStripe\Queue\Jobs\SyncJob(new Injector(), function () {
            $_SERVER['__queue.closure'] = true;
        }, 'data');
        $job->run();

        $this->assertTrue($_SERVER['__queue.closure']);
    }

    public function testRunResolvesAndRunsJobClassWithCorrectMethod()
    {
        $injector = m::mock('Injector');
        $job = new \StudioBonito\SilverStripe\Queue\Jobs\SyncJob($injector, 'Foo@bar', '"data"');
        $handler = m::mock('StdClass');
        $injector->shouldReceive('get')->once()->with('Foo')->andReturn($handler);
        $handler->shouldReceive('bar')->once()->with($job, 'data');

        $job->run();
    }

}
 