<?php namespace StudioBonito\SilverStripe\Queue\Tests;

use Mockery as m;
use StudioBonito\SilverStripe\Queue\QueueManager;

class QueueManagerTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testDefaultConnectionCanBeResolved()
    {
        $manager = new QueueManager(array());

        $config = m::mock('StdClass');
        $connector = m::mock('StdClass');
        $queue = m::mock('StdClass');

        $config->shouldReceive('get')->once()->with('sync')->andReturn(array('driver' => 'sync'));
        $connector->shouldReceive('connect')->once()->with(array('driver' => 'sync'))->andReturn($queue);

        $manager->setConfig($config);

        $manager->addConnector(
            'sync',
            function () use ($connector) {
                return $connector;
            }
        );
        $queue->shouldReceive('setInjector')->once();

        $this->assertTrue($queue === $manager->connection('sync'));
    }

    public function testOtherConnectionCanBeResolved()
    {
        $manager = new QueueManager(array());

        $config = m::mock('StdClass');
        $connector = m::mock('StdClass');
        $queue = m::mock('StdClass');

        $config->shouldReceive('get')->once()->with('foo')->andReturn(array('driver' => 'bar'));
        $connector->shouldReceive('connect')->once()->with(array('driver' => 'bar'))->andReturn($queue);

        $manager->setConfig($config);

        $manager->addConnector(
            'bar',
            function () use ($connector) {
                return $connector;
            }
        );
        $queue->shouldReceive('setInjector')->once();

        $this->assertTrue($queue === $manager->connection('foo'));
    }
}
 