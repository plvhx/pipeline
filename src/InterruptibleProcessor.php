<?php

namespace Gandung\Pipeline;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class InterruptibleProcessor implements ProcessorInterface
{
    use ProcessorTrait;

    /**
     * @var integer
     */
    private $state = self::STATE_RUNNING;

    /**
     * @var \Closure
     */
    private $routine;

    public function __construct(callable $routine)
    {
        $this->routine = $routine;
    }

    /**
     * {@inheritdoc}
     */
    public function invoke($tasks, $param)
    {
        if ($this->getState() === static::STATE_INTERRUPTED) {
            return $param;
        }

        while (($task = array_shift($tasks)) !== null) {
            $param = call_user_func($task, $param);

            if (call_user_func($this->routine, $param) !== true) {
                $this->interrupt();

                return $param;
            }
        }

        $this->freeze();
        
        return $param;
    }

    /**
     * {@inheritdoc}
     */
    public function resume()
    {
        if ($this->getState() === static::STATE_INTERRUPTED) {
            throw new ProcessException(
                "Cannot continuing interrupted task."
            );
        }

        if ($this->getState() === static::STATE_RUNNING) {
            throw new ProcessException(
                "Current execution task state is already running."
            );
        }

        $this->setState(static::STATE_RUNNING);
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
    public function interrupt()
    {
        if ($this->getState() === static::STATE_INTERRUPTED) {
            throw new ProcessException(
                "Current execution task state is already interrupted."
            );
        }

        $this->setState(static::STATE_INTERRUPTED);
    }

    /**
     * {@inheritdoc}
     */
    public function pause()
    {
        throw new ProcessException(
            "Current execution task can't be paused."
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getState()
    {
        return $this->state;
    }
}
