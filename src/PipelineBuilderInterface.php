<?php

namespace Gandung\Pipeline;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
interface PipelineBuilderInterface
{
    /**
     * Add the closure or invokable class instance. This method must
     * retain the state of the current instance to accomplish immutability.
     *
     * @param \Closure|object $task
     * @return $this
     */
    public function add($task);

    /**
     * Build all registered task into single invokable object.
     *
     * @param ProcessorInterface $processor
     * @return PipelineInterface
     */
    public function build(ProcessorInterface $processor = null);
}
