<?php namespace StudioBonito\SilverStripe\Queue\Tests;

use Mockery as m;

class SyncQueueTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testPushShouldRunJobInstantly()
    {
        $sync = $this->getMock('StudioBonito\SilverStripe\Queue\SyncQueue', array('resolveJob'));
        $job = m::mock('StdClass');
        $sync->expects($this->once())->method('resolveJob')->with(
            $this->equalTo('Foo'),
            $this->equalTo('{"foo":"foobar"}')
        )->will($this->returnValue($job));
        $job->shouldReceive('run')->once();

        $sync->push('Foo', array('foo' => 'foobar'));
    }
}
 