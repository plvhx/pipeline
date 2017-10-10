<?php

namespace Gandung\Pipeline;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class PipelineBuilder implements PipelineBuilderInterface
{
    /**
     * @var array
     */
    private $tasks = [];

    /**
     * {@inheritdoc}
     */
    public function add($task)
    {
        $q = clone $this;
        $q->tasks[] = $task;

        return $q;
    }

    /**
     * {@inheritdoc}
     */
    public function build(ProcessorInterface $processor = null)
    {
        return new Pipeline($this->tasks, $processor);
    }
}
