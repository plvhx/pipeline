<?php

namespace Gandung\Pipeline\Tests;

use PHPUnit\Framework\TestCase;
use Gandung\Pipeline\Processor;

class ProcessorTest extends TestCase
{
    public function testCanGetInstance()
    {
        $processor = new Processor;
        $this->assertInstanceOf(Processor::class, $processor);
    }

    public function testCanInvokeClosureBasedTasks()
    {
        $tasks = [
            function ($param) {
                return $param;
            },
            function ($param) {
                return join(' ', [$param, 'bar']);
            },
            function ($param) {
                return join(' ', [$param, 'baz']);
            }
        ];
        $processor = new Processor;
        $this->assertInstanceOf(Processor::class, $processor);
        $payload = $processor->invoke($tasks, 'foo');
        $this->assertInternalType('string', $payload);
        $this->assertEquals('foo bar baz', $payload);
    }

    public function testCanInvokeInstanceBasedTasks()
    {
        $tasks = [
            new Fixtures\FooTask,
            new Fixtures\BarTask,
            new Fixtures\BazTask
        ];
        $processor = new Processor;
        $this->assertInstanceOf(Processor::class, $processor);
        $payload = $processor->invoke($tasks, 'foo');
        $this->assertInternalType('string', $payload);
        $this->assertEquals('foo bar baz', $payload);
    }

    /**
     * @expectedException \Gandung\Pipeline\ProcessException
     */
    public function testCanRaiseExceptionWhenTryToResumeRunningTask()
    {
        $processor = new Processor;
        $this->assertInstanceOf(Processor::class, $processor);
        $this->assertEquals(Processor::STATE_RUNNING, $processor->getState());
        $processor->resume();
    }

    public function testCanResumeFreezedTasks()
    {
        $processor = new Processor;
        $this->assertInstanceOf(Processor::class, $processor);
        $this->assertEquals(Processor::STATE_RUNNING, $processor->getState());
        $processor->freeze();
        $this->assertEquals(Processor::STATE_FREEZED, $processor->getState());
        $processor->resume();
        $this->assertEquals(Processor::STATE_RUNNING, $processor->getState());
    }

    /**
     * @expectedException \Gandung\Pipeline\ProcessException
     */
    public function testCanRaiseExceptionWhenTryToFreezeFreezedTask()
    {
        $processor = new Processor;
        $this->assertInstanceOf(Processor::class, $processor);
        $processor->freeze();
        $this->assertEquals(Processor::STATE_FREEZED, $processor->getState());
        $processor->freeze();
    }

    /**
     * @expectedException \Gandung\Pipeline\ProcessException
     */
    public function testCanRaiseExceptionWhenTryToInterruptNonInterruptibleTask()
    {
        $processor = new Processor;
        $this->assertInstanceOf(Processor::class, $processor);
        $processor->interrupt();
    }

    /**
     * @expectedException \Gandung\Pipeline\ProcessException
     */
    public function testCanRaiseExceptionWhenTryToPauseNonIntermitTask()
    {
        $processor = new Processor;
        $this->assertInstanceOf(Processor::class, $processor);
        $processor->pause();
    }
}
