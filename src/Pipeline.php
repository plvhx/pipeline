<?php

namespace Gandung\Pipeline;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class Pipeline implements PipelineInterface
{
    /**
     * @var array
     */
    private $tasks = [];

    /**
     * @var ProcessorInterface
     */
    private $processor;

    public function __construct($tasks = [], ProcessorInterface $processor = null)
    {
        $this->tasks = $tasks;
        $this->processor = is_null($processor)
            ? new Processor
            : $processor;
    }

    /**
     * {@inheritdoc}
     */
    public function pipe($task)
    {
        $q = clone $this;
        $q->tasks[] = $task;

        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function invokeAll($param)
    {
        return $this->processor->invoke($this->tasks, $param);
    }

    public function __invoke($param)
    {
        return $this->processor->invoke($this->tasks, $param);
    }
}
