<?php

namespace Gandung\Pipeline;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class IntermitProcessor implements ProcessorInterface
{
    use ProcessorTrait;

    /**
     * @var integer
     */
    private $state = self::STATE_RUNNING;

    /**
     * @var mixed
     */
    private $saved;

    /**
     * @var \Closure
     */
    private $routine;

    /**
     * @var array
     */
    private $tasks;

    public function __construct(callable $routine = null)
    {
        $this->routine = $routine;
    }

    /**
     * {@inheritdoc}
     */
    public function invoke($tasks, $param)
    {
        if ($this->getState() === static::STATE_PAUSED) {
            if (is_null($this->tasks)) {
                $this->saveValueFromPausedState($param);
            }

            return $this->saved;
        }

        if ($this->getState() === static::STATE_RUNNING &&
            !empty($this->tasks) &&
            !is_null($this->saved)) {
            return $this->resumePendingTask();
        }

        while (($task = array_shift($tasks)) !== null) {
            $param = call_user_func($task, $param);

            if (is_callable($this->routine) && call_user_func($this->routine, $param) !== true) {
                $this->saveValueFromPausedState($param);
                $this->saveTasksFromPausedState($tasks);
                $this->pause();

                return $param;
            }
        }

        return $param;
    }

    /**
     * {@inheritdoc}
     */
    public function resume()
    {
        if ($this->getState() === static::STATE_RUNNING) {
            throw new ProcessException(
                "Current execution task is already running."
            );
        }

        $this->setState(static::STATE_RUNNING);
    }

    /**
     * {@inheritdoc}
     */
    public function pause()
    {
        if ($this->getState() === static::STATE_PAUSED) {
            throw new ProcessException(
                "Current execution task is already paused."
            );
        }

        $this->setState(static::STATE_PAUSED);
    }

    /**
     * {@inheritdoc}
     */
    public function interrupt()
    {
        throw new ProcessException(
            "current execution task cannot be interrupted."
        );
    }

    /**
     * {@inheritdoc}
     */
    public function freeze()
    {
        if ($this->getState() === static::STATE_FREEZED) {
            throw new ProcessException(
                "Current execution task state is already freezed."
            );
        }

        $this->setState(static::STATE_FREEZED);
    }

    /**
     * {@inheritdoc}
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Save current value in paused state.
     *
     * @param mixed $param
     * @return void
     */
    private function saveValueFromPausedState($param)
    {
        $this->saved = $param;
    }

    /**
     * Save current tasks in paused state.
     *
     * @param array $tasks
     * @return void
     */
    private function saveTasksFromPausedState($tasks)
    {
        $this->tasks = $tasks;
    }

    /**
     * Continue invocation of pending task.
     *
     * @return mixed
     */
    private function resumePendingTask()
    {
        while (($task = array_shift($this->tasks)) !== null) {
            $this->saved = call_user_func($task, $this->saved);
        }

        $this->freeze();

        return $this->saved;
    }
}
