<?php namespace StudioBonito\SilverStripe\Queue\Tests;

use Mockery as m;

class BeanstalkdQueueTest extends TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testPushProperlyPushesJobOntoBeanstalkd()
    {
        $queue = new \StudioBonito\SilverStripe\Queue\BeanstalkdQueue(m::mock('Pheanstalk_Pheanstalk'), 'default', 60);
        $pheanstalk = $queue->getPheanstalk();
        $pheanstalk->shouldReceive('useTube')->once()->with('stack')->andReturn($pheanstalk);
        $pheanstalk->shouldReceive('useTube')->once()->with('default')->andReturn($pheanstalk);
        $pheanstalk->shouldReceive('put')->twice()->with(
            json_encode(array('job' => 'foo', 'data' => array('data'))),
            1024,
            0,
            60
        );

        $queue->push('foo', array('data'), 'stack');
        $queue->push('foo', array('data'));
    }

    public function testDelayedPushProperlyPushesJobOntoBeanstalkd()
    {
        $queue = new \StudioBonito\SilverStripe\Queue\BeanstalkdQueue(m::mock('Pheanstalk_Pheanstalk'), 'default', 60);
        $pheanstalk = $queue->getPheanstalk();
        $pheanstalk->shouldReceive('useTube')->once()->with('stack')->andReturn($pheanstalk);
        $pheanstalk->shouldReceive('useTube')->once()->with('default')->andReturn($pheanstalk);
        $pheanstalk->shouldReceive('put')->twice()->with(
            json_encode(array('job' => 'foo', 'data' => array('data'))),
            \Pheanstalk_Pheanstalk::DEFAULT_PRIORITY,
            5
        );

        $queue->later(5, 'foo', array('data'), 'stack');
        $queue->later(5, 'foo', array('data'));
    }

    public function testPopProperlyPopsJobOffOfBeanstalkd()
    {
        $queue = new \StudioBonito\SilverStripe\Queue\BeanstalkdQueue(m::mock('Pheanstalk_Pheanstalk'), 'default', 60);
        $queue->setInjector(m::mock('Injector'));
        $pheanstalk = $queue->getPheanstalk();
        $pheanstalk->shouldReceive('watchOnly')->once()->with('default')->andReturn($pheanstalk);
        $job = m::mock('Pheanstalk_Job');
        $pheanstalk->shouldReceive('reserve')->once()->andReturn($job);

        $result = $queue->pop();

        $this->assertInstanceOf('StudioBonito\SilverStripe\Queue\Jobs\BeanstalkdJob', $result);
    }
} 