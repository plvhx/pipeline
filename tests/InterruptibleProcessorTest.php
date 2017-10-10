<?php

namespace Gandung\Pipeline\Tests;

use PHPUnit\Framework\TestCase;
use Gandung\Pipeline\InterruptibleProcessor;

class InterruptibleProcessorTest extends TestCase
{
    public function testCanGetInstanceWithDummyCancellationRoutine()
    {
        $processor = new InterruptibleProcessor(
            function ($param) {
                return $param != 1337;
            }
        );
        $this->assertInstanceOf(InterruptibleProcessor::class, $processor);
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
        $processor = new InterruptibleProcessor(
            function ($param) {
                return $param !== 'foo bar';
            }
        );
        $payload = $processor->invoke($tasks, 'foo');
        $this->assertInternalType('string', $payload);
        $this->assertEquals('foo bar', $payload);
        $this->assertEquals(InterruptibleProcessor::STATE_INTERRUPTED, $processor->getState());
    }

    public function testCanInvokeInstanceBasedTasks()
    {
        $tasks = [
            new Fixtures\FooTask,
            new Fixtures\BarTask,
            new Fixtures\BazTask
        ];
        $processor = new InterruptibleProcessor(
            function ($param) {
                return $param !== 'foo bar';
            }
        );
        $payload = $processor->invoke($tasks, 'foo');
        $this->assertInternalType('string', $payload);
        $this->assertEquals('foo bar', $payload);
        $this->assertEquals(InterruptibleProcessor::STATE_INTERRUPTED, $processor->getState());
    }

    public function testCanInvokeAllTasksEvenWithCancellableRoutineDefined()
    {
        $tasks = [
            new Fixtures\FooTask,
            new Fixtures\BarTask,
            new Fixtures\BazTask
        ];
        $processor = new InterruptibleProcessor(
            function ($param) {
                return $param !== 'foo bar baz buz';
            }
        );
        $payload = $processor->invoke($tasks, 'foo');
        $this->assertInternalType('string', $payload);
        $this->assertEquals('foo bar baz', $payload);
        $this->assertEquals(InterruptibleProcessor::STATE_FREEZED, $processor->getState());
    }

    public function testCanImmediatelyReturnWhenChangeStateIntoInterrupted()
    {
        $tasks = [
            new Fixtures\FooTask,
            new Fixtures\BarTask,
            new Fixtures\BazTask
        ];
        $processor = new InterruptibleProcessor(
            function ($param) {
                return $param !== 'foo bar';
            }
        );
        $this->assertInstanceOf(InterruptibleProcessor::class, $processor);
        $processor->interrupt();
        $this->assertEquals(InterruptibleProcessor::STATE_INTERRUPTED, $processor->getState());
        $payload = $processor->invoke($tasks, 'foo');
        $this->assertInternalType('string', $payload);
        $this->assertEquals('foo', $payload);
    }

    /**
     * @expectedException \Gandung\Pipeline\ProcessException
     */
    public function testCanRaiseExceptionWhenTryToResumeInterruptedTask()
    {
        $processor = new InterruptibleProcessor(
            function ($param) {
                return $param !== 1337;
            }
        );
        $this->assertInstanceOf(InterruptibleProcessor::class, $processor);
        $processor->interrupt();
        $this->assertEquals(InterruptibleProcessor::STATE_INTERRUPTED, $processor->getState());
        $processor->resume();
    }

    /**
     * @expectedException \Gandung\Pipeline\ProcessException
     */
    public function testCanRaiseExceptionWhenTryToResumeRunningTask()
    {
        $processor = new InterruptibleProcessor(
            function ($param) {
                return $param !== 1337;
            }
        );
        $this->assertInstanceOf(InterruptibleProcessor::class, $processor);
        $processor->resume();
    }

    public function testCanResumeFreezedTask()
    {
        $processor = new InterruptibleProcessor(
            function ($param) {
                return $param !== 1337;
            }
        );
        $this->assertInstanceOf(InterruptibleProcessor::class, $processor);
        $processor->freeze();
        $this->assertEquals(InterruptibleProcessor::STATE_FREEZED, $processor->getState());
        $processor->resume();
        $this->assertEquals(InterruptibleProcessor::STATE_RUNNING, $processor->getState());
    }

    /**
     * @expectedException \Gandung\Pipeline\ProcessException
     */
    public function testCanRaiseExceptionWhenTryToFreezeFreezedTask()
    {
        $processor = new InterruptibleProcessor(
            function ($param) {
                return $param !== 1337;
            }
        );
        $this->assertInstanceOf(InterruptibleProcessor::class, $processor);
        $processor->freeze();
        $this->assertEquals(InterruptibleProcessor::STATE_FREEZED, $processor->getState());
        $processor->freeze();
    }

    /**
     * @expectedException \Gandung\Pipeline\ProcessException
     */
    public function testCanRaiseExceptionWhenTryToInterruptInterruptedTask()
    {
        $processor = new InterruptibleProcessor(
            function ($param) {
                return $param !== 1337;
            }
        );
        $this->assertInstanceOf(InterruptibleProcessor::class, $processor);
        $processor->interrupt();
        $this->assertEquals(InterruptibleProcessor::STATE_INTERRUPTED, $processor->getState());
        $processor->interrupt();
    }

    /**
     * @expectedException \Gandung\Pipeline\ProcessException
     */
    public function testCanRaiseExceptionWhenForcePausingInterruptibleTask()
    {
        $processor = new InterruptibleProcessor(
            function ($param) {
                return $param !== 1337;
            }
        );
        $this->assertInstanceOf(InterruptibleProcessor::class, $processor);
        $processor->pause();
    }
}
