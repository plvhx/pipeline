<?php

namespace Gandung\Pipeline\Tests;

use PHPUnit\Framework\TestCase;
use Gandung\Pipeline\Pipeline;

class PipelineTest extends TestCase
{
    public function testCanGetInstance()
    {
        $pipe = new Pipeline;
        $this->assertInstanceOf(Pipeline::class, $pipe);
    }

    public function testCanPipeClosureBasedTasks()
    {
        $pipe = (new Pipeline)
            ->pipe(function ($param) {
                return $param;
            })
            ->pipe(function ($param) {
                return join(' ', [$param, 'bar']);
            })
            ->pipe(function ($param) {
                return join(' ', [$param, 'baz']);
            });
        $this->assertInstanceOf(Pipeline::class, $pipe);
        $payload = $pipe->invokeAll('foo');
        $this->assertInternalType('string', $payload);
        $this->assertEquals('foo bar baz', $payload);
    }

    public function testCanPipeInstanceBasedTasks()
    {
        $pipe = (new Pipeline)
            ->pipe(new Fixtures\FooTask)
            ->pipe(new Fixtures\BarTask)
            ->pipe(new Fixtures\BazTask);
        $this->assertInstanceOf(Pipeline::class, $pipe);
        $payload = $pipe->invokeAll('foo');
        $this->assertInternalType('string', $payload);
        $this->assertEquals('foo bar baz', $payload);
    }

    public function testCanInvokeAllTaskUsingMagicMethod()
    {
        $pipe = (new Pipeline)
            ->pipe(new Fixtures\FooTask)
            ->pipe(new Fixtures\BarTask)
            ->pipe(new Fixtures\BazTask);
        $this->assertInstanceOf(Pipeline::class, $pipe);
        $payload = $pipe('foo');
        $this->assertInternalType('string', $payload);
        $this->assertEquals('foo bar baz', $payload);
    }
}
