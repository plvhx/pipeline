<?php

namespace Gandung\Pipeline;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
interface ProcessorInterface
{
    /**
     * Indicates that current execution state is running.
     */
    const STATE_RUNNING = 1;

    /**
     * Indicates that current execution state is interrupted.
     */
    const STATE_INTERRUPTED = 2;

    /**
     * Indicates that current execution state is paused.
     */
    const STATE_PAUSED = 4;

    /**
     * Indicates that current execution state is freezed.
     */
    const STATE_FREEZED = 8;

    /**
     * Invoke task and chain it's result into
     * another task.
     *
     * @param array $tasks
     * @param mixed $param
     * @return mixed
     */
    public function invoke($tasks, $param);

    /**
     * Continue the current paused execution state.
     */
    public function resume();

    /**
     * Freeze the current execution state.
     */
    public function freeze();

    /**
     * Interrupt the current running execution state.
     */
    public function interrupt();

    /**
     * Pause the current execution state.
     */
    public function pause();
    
    /**
     * Get execution state.
     *
     * @return integer
     */
    public function getState();
}
