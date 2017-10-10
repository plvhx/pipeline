<?php

namespace Gandung\Pipeline;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
interface PipelineInterface
{
    /**
     * Appending new task into task queueing stack.
     *
     * @param \Closure|object $task
     * @return $this
     */
    public function pipe($task);

    /**
     * Execute all task in parallel.
     *
     * @param mixed $param
     * @return mixed
     */
    public function invokeAll($param);
}
