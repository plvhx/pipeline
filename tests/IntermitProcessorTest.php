<?php

namespace Gandung\Pipeline\Tests;

use PHPUnit\Framework\TestCase;
use Gandung\Pipeline\IntermitProcessor;

class IntermitProcessorTest extends TestCase
{
    public function testCanGetInstance()
    {
        $processor = new IntermitProcessor(
            function ($param) {
                return $param === 1337;
            }
        );
        $this->assertInstanceOf(IntermitProcessor::class, $processor);
    }

    public function testCanInParallelInvokeClosureBasedTasks()
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
        $processor = new IntermitProcessor;
        $this->assertInstanceOf(IntermitProcessor::class, $processor);
        $payload = $processor->invoke($tasks, 'foo');
        $this->assertInternalType('string', $payload);
        $this->assertEquals('foo bar baz', $payload);
    }

    public function testCanInParallelInvokeInstanceBasedTasks()
    {
        $tasks = [
            new Fixtures\FooTask,
            new Fixtures\BarTask,
            new Fixtures\BazTask
        ];
        $processor = new IntermitProcessor;
        $this->assertInstanceOf(IntermitProcessor::class, $processor);
        $payload = $processor->invoke($tasks, 'foo');
        $this->assertInternalType('string', $payload);
        $this->assertEquals('foo bar baz', $payload);
    }

    public function testCanImmediatelyReturnWhenStateIsPaused()
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
        $processor = new IntermitProcessor;
        $this->assertInstanceOf(IntermitProcessor::class, $processor);
        $processor->pause();
        $payload = $processor->invoke($tasks, 'foo');
        $this->assertInternalType('string', $payload);
        $this->assertEquals('foo', $payload);
    }

    public function testCanImmediatelyPauseExecutionByAssertionRoutine()
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
        $processor = new IntermitProcessor(
            function ($param) {
                return $param !== 'foo bar';
            }
        );
        $this->assertInstanceOf(IntermitProcessor::class, $processor);
        $payload = $processor->invoke($tasks, 'foo');
        $this->assertInternalType('string', $payload);
        $this->assertEquals('foo bar', $payload);
        $this->assertEquals(IntermitProcessor::STATE_PAUSED, $processor->getState());
    }

    /**
     * @expectedException \Gandung\Pipeline\ProcessException
     */
    public function testCanRaiseExceptionWhenChangeAlreadyRunningProcessIntoRunnableState()
    {
        $processor = new IntermitProcessor;
        $this->assertInstanceOf(IntermitProcessor::class, $processor);
        $processor->resume();
    }

    public function testCanChangeProcessStateFromPausedToRunning()
    {
        $processor = new IntermitProcessor;
        $this->assertInstanceOf(IntermitProcessor::class, $processor);
        $this->assertEquals(IntermitProcessor::STATE_RUNNING, $processor->getState());
        $processor->pause();
        $this->assertEquals(IntermitProcessor::STATE_PAUSED, $processor->getState());
        $processor->resume();
        $this->assertEquals(IntermitProcessor::STATE_RUNNING, $processor->getState());
    }

    /**
     * @expectedException \Gandung\Pipeline\ProcessException
     */
    public function testCanRaiseExceptionWhenChangeAlreadyPausedProcessIntoPausedState()
    {
        $processor = new IntermitProcessor;
        $this->assertInstanceOf(IntermitProcessor::class, $processor);
        $this->assertEquals(IntermitProcessor::STATE_RUNNING, $processor->getState());
        $processor->pause();
        $this->assertEquals(IntermitProcessor::STATE_PAUSED, $processor->getState());
        $processor->pause();
    }

    public function testCanChangeProcessStateFromRunningToPaused()
    {
        $processor = new IntermitProcessor;
        $this->assertInstanceOf(IntermitProcessor::class, $processor);
        $this->assertEquals(IntermitProcessor::STATE_RUNNING, $processor->getState());
        $processor->pause();
        $this->assertEquals(IntermitProcessor::STATE_PAUSED, $processor->getState());
    }

    /**
     * @expectedException \Gandung\Pipeline\ProcessException
     */
    public function testCanRaiseExceptionWhenForceInterruptNonInterruptingProcess()
    {
        $processor = new IntermitProcessor;
        $this->assertInstanceOf(IntermitProcessor::class, $processor);
        $processor->interrupt();
    }

    /**
     * @expectedException \Gandung\Pipeline\ProcessException
     */
    public function testCanRaiseExceptionWhenChangeAlreadyFreezedProcessIntoFreezedState()
    {
        $processor = new IntermitProcessor;
        $this->assertInstanceOf(IntermitProcessor::class, $processor);
        $this->assertEquals(IntermitProcessor::STATE_RUNNING, $processor->getState());
        $processor->freeze();
        $this->assertEquals(IntermitProcessor::STATE_FREEZED, $processor->getState());
        $processor->freeze();
    }

    public function testCanResumePendingTask()
    {
        $tasks = [
            new Fixtures\FooTask,
            new Fixtures\BarTask,
            new Fixtures\BazTask
        ];
        $processor = new IntermitProcessor(
            function ($param) {
                return $param !== 'foo bar';
            }
        );
        $this->assertInstanceOf(IntermitProcessor::class, $processor);
        $payload = $processor->invoke($tasks, 'foo');
        $this->assertInternalType('string', $payload);
        $this->assertEquals('foo bar', $payload);
        $this->assertEquals(IntermitProcessor::STATE_PAUSED, $processor->getState());
        $processor->resume();
        $payload = $processor->invoke($tasks, 'foo');
        $this->assertInternalType('string', $payload);
        $this->assertEquals('foo bar baz', $payload);
        $this->assertEquals(IntermitProcessor::STATE_FREEZED, $processor->getState());
    }
}
