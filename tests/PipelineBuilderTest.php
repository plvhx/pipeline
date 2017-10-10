<?php

namespace Gandung\Pipeline\Tests;

use PHPUnit\Framework\TestCase;
use Gandung\Pipeline\Pipeline;
use Gandung\Pipeline\PipelineBuilder;

class PipelineBuilderTest extends TestCase
{
    public function testCanGetInstance()
    {
        $builder = new PipelineBuilder;
        $this->assertInstanceOf(PipelineBuilder::class, $builder);
    }

    public function testCanAddClosureBasedTasks()
    {
        $builder = (new PipelineBuilder)
            ->add(function ($param) {
                return $param;
            })
            ->add(function ($param) {
                return join(' ', [$param, 'bar']);
            })
            ->add(function ($param) {
                return join(' ', [$param, 'baz']);
            });
        $this->assertInstanceOf(PipelineBuilder::class, $builder);
        $pipe = $builder->build();
        $this->assertInstanceOf(Pipeline::class, $pipe);
        $payload = $pipe->invokeAll('foo');
        $this->assertInternalType('string', $payload);
        $this->assertEquals('foo bar baz', $payload);
    }

    public function testCanAddInstanceBasedTasks()
    {
        $builder = (new PipelineBuilder)
            ->add(new Fixtures\FooTask)
            ->add(new Fixtures\BarTask)
            ->add(new Fixtures\BazTask);
        $this->assertInstanceOf(PipelineBuilder::class, $builder);
        $pipe = $builder->build();
        $this->assertInstanceOf(Pipeline::class, $pipe);
        $payload = $pipe->invokeAll('foo');
        $this->assertInternalType('string', $payload);
        $this->assertEquals('foo bar baz', $payload);
    }
}
