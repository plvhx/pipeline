<?php

namespace Gandung\Pipeline;

trait ProcessorTrait
{
    /**
     * Set current state.
     *
     * @param integer $state
     * @return void
     */
    private function setState($state)
    {
        if (!$this->validateState($state)) {
            throw new ProcessException(
                "Invalid task execution state."
            );
        }

        $this->state = $state;
    }

    /**
     * Validate new task state before passing it to the setter.
     *
     * @param integer $state
     * @return boolean
     */
    private function validateState($state)
    {
        if ($state !== static::STATE_RUNNING &&
            $state !== static::STATE_INTERRUPTED &&
            $state !== static::STATE_PAUSED &&
            $state !== static::STATE_FREEZED) {
            return false;
        }

        return true;
    }
}
