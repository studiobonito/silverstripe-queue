<?php namespace StudioBonito\SilverStripe\Queue\Tests;

use Mockery as m;

class WorkerTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testJobIsPoppedOffQueueAndProcessed()
    {
        $worker = $this->getMock(
            'StudioBonito\SilverStripe\Queue\Worker',
            array('process'),
            array($manager = m::mock('StudioBonito\SilverStripe\Queue\QueueManager'))
        );
        $manager->shouldReceive('connection')->once()->with('connection')->andReturn($connection = m::mock('StdClass'));
        $manager->shouldReceive('getName')->andReturn('connection');
        $job = m::mock('StudioBonito\SilverStripe\Queue\Jobs\AbstractJob');
        $connection->shouldReceive('pop')->once()->with('queue')->andReturn($job);
        $worker->expects($this->once())->method('process')->with(
            $this->equalTo('connection'),
            $this->equalTo($job),
            $this->equalTo(0),
            $this->equalTo(0)
        );

        $worker->pop('connection', 'queue');
    }

    public function testJobIsPoppedOffFirstQueueInListAndProcessed()
    {
        $worker = $this->getMock(
            'StudioBonito\SilverStripe\Queue\Worker',
            array('process'),
            array($manager = m::mock('StudioBonito\SilverStripe\Queue\QueueManager'))
        );
        $manager->shouldReceive('connection')->once()->with('connection')->andReturn($connection = m::mock('StdClass'));
        $manager->shouldReceive('getName')->andReturn('connection');
        $job = m::mock('StudioBonito\SilverStripe\Queue\Jobs\AbstractJob');
        $connection->shouldReceive('pop')->once()->with('queue1')->andReturn(null);
        $connection->shouldReceive('pop')->once()->with('queue2')->andReturn($job);
        $worker->expects($this->once())->method('process')->with(
            $this->equalTo('connection'),
            $this->equalTo($job),
            $this->equalTo(0),
            $this->equalTo(0)
        );

        $worker->pop('connection', 'queue1,queue2');
    }

    public function testWorkerSleepsIfNoJobIsPresentAndSleepIsEnabled()
    {
        $worker = $this->getMock(
            'StudioBonito\SilverStripe\Queue\Worker',
            array('process', 'sleep'),
            array($manager = m::mock('StudioBonito\SilverStripe\Queue\QueueManager'))
        );
        $manager->shouldReceive('connection')->once()->with('connection')->andReturn($connection = m::mock('StdClass'));
        $connection->shouldReceive('pop')->once()->with('queue')->andReturn(null);
        $worker->expects($this->never())->method('process');
        $worker->expects($this->once())->method('sleep')->with($this->equalTo(1));

        $worker->pop('connection', 'queue', 0, 128, true);
    }

    public function testWorkerLogsJobToFailedQueueIfMaxTriesHasBeenExceeded()
    {
        $worker = new \StudioBonito\SilverStripe\Queue\Worker(m::mock(
                                                                  'StudioBonito\SilverStripe\Queue\QueueManager'
                                                              ), $failer = m::mock(
            'StudioBonito\SilverStripe\Queue\Failed\FailedJobProviderInterface'
        ));
        $job = m::mock('StudioBonito\SilverStripe\Queue\Jobs\AbstractJob');
        $job->shouldReceive('attempts')->once()->andReturn(10);
        $job->shouldReceive('getQueue')->once()->andReturn('queue');
        $job->shouldReceive('getRawPayload')->once()->andReturn('body');
        $job->shouldReceive('delete')->once();
        $failer->shouldReceive('log')->once()->with('connection', 'queue', 'body');

        $worker->process('connection', $job, 3, 0);
    }

    public function testProcessRunsJobAndAutoDeletesIfTrue()
    {
        $worker = new \StudioBonito\SilverStripe\Queue\Worker(m::mock('StudioBonito\SilverStripe\Queue\QueueManager'));
        $job = m::mock('StudioBonito\SilverStripe\Queue\Jobs\AbstractJob');
        $job->shouldReceive('run')->once();
        $job->shouldReceive('autoDelete')->once()->andReturn(true);
        $job->shouldReceive('delete')->once();

        $worker->process('connection', $job, 0, 0);
    }

    public function testProcessRunsJobAndDoesntCallDeleteIfJobDoesntAutoDelete()
    {
        $worker = new \StudioBonito\SilverStripe\Queue\Worker(m::mock('StudioBonito\SilverStripe\Queue\QueueManager'));
        $job = m::mock('StudioBonito\SilverStripe\Queue\Jobs\AbstractJob');
        $job->shouldReceive('run')->once();
        $job->shouldReceive('autoDelete')->once()->andReturn(false);
        $job->shouldReceive('delete')->never();

        $worker->process('connection', $job, 0, 0);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testJobIsReleasedWhenExceptionIsThrown()
    {
        $worker = new \StudioBonito\SilverStripe\Queue\Worker(m::mock('StudioBonito\SilverStripe\Queue\QueueManager'));
        $job = m::mock('StudioBonito\SilverStripe\Queue\Jobs\AbstractJob');
        $job->shouldReceive('run')->once()->andReturnUsing(
            function () {
                throw new \RuntimeException;
            }
        );
        $job->shouldReceive('isDeleted')->once()->andReturn(false);
        $job->shouldReceive('release')->once()->with(5);

        $worker->process('connection', $job, 0, 5);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testJobIsNotReleasedWhenExceptionIsThrownButJobIsDeleted()
    {
        $worker = new \StudioBonito\SilverStripe\Queue\Worker(m::mock('StudioBonito\SilverStripe\Queue\QueueManager'));
        $job = m::mock('StudioBonito\SilverStripe\Queue\Jobs\AbstractJob');
        $job->shouldReceive('run')->once()->andReturnUsing(
            function () {
                throw new \RuntimeException;
            }
        );
        $job->shouldReceive('isDeleted')->once()->andReturn(true);
        $job->shouldReceive('release')->never();

        $worker->process('connection', $job, 0, 5);
    }
}
 