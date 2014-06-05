<?php namespace StudioBonito\SilverStripe\Queue\Tests;

use Mockery as m;

class DbJobTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testRunProperlyCallsTheJobHandler()
    {
        $job = $this->getJob();
        $job->getInjector()->shouldReceive('get')->once()->with('foo')->andReturn($handler = m::mock('StdClass'));
        $handler->shouldReceive('run')->once()->with($job, array('data'));

        $job->run();
    }

    protected function getJob()
    {
        $jobQueue = m::mock('StudioBonito\SilverStripe\Queue\Models\JobQueue');

        $jobQueue->shouldReceive('hasMethod')->with('getPayload')->andReturn(true);

        $jobQueue->shouldReceive('getPayload')->andReturn(
            json_encode(array('job' => 'foo', 'data' => array('data'), 'attempts' => 1))
        );

        return new \StudioBonito\SilverStripe\Queue\Jobs\DbJob(
            m::mock('Injector'),
            m::mock('StudioBonito\SilverStripe\Queue\DbQueue'),
            $jobQueue,
            'default'
        );
    }
}
 