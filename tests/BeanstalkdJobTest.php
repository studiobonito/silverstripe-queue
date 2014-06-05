<?php namespace StudioBonito\SilverStripe\Queue\Tests;

use Mockery as m;

class BeanstalkdJobTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testRunProperlyCallsTheJobHandler()
    {
        $job = $this->getJob();
        $job->getPheanstalkJob()->shouldReceive('getData')->once()->andReturn(
            json_encode(array('job' => 'foo', 'data' => array('data')))
        );
        $job->getInjector()->shouldReceive('get')->once()->with('foo')->andReturn($handler = m::mock('StdClass'));
        $handler->shouldReceive('run')->once()->with($job, array('data'));

        $job->run();
    }

    public function testDeleteRemovesTheJobFromBeanstalkd()
    {
        $job = $this->getJob();
        $job->getPheanstalk()->shouldReceive('delete')->once()->with($job->getPheanstalkJob());

        $job->delete();
    }

    public function testReleaseProperlyReleasesJobOntoBeanstalkd()
    {
        $job = $this->getJob();
        $job->getPheanstalk()->shouldReceive('release')->once()->with(
            $job->getPheanstalkJob(),
            \Pheanstalk_Pheanstalk::DEFAULT_PRIORITY,
            0
        );

        $job->release();
    }

    public function testBuryProperlyBuryTheJobFromBeanstalkd()
    {
        $job = $this->getJob();
        $job->getPheanstalk()->shouldReceive('bury')->once()->with($job->getPheanstalkJob());

        $job->bury();
    }

    protected function getJob()
    {
        return new \StudioBonito\SilverStripe\Queue\Jobs\BeanstalkdJob(
            m::mock('Injector'),
            m::mock('Pheanstalk_Pheanstalk'),
            m::mock('Pheanstalk_Job'),
            'default'
        );
    }
}
 