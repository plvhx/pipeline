<?php

namespace Gandung\Pipeline;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class Processor implements ProcessorInterface
{
    use ProcessorTrait;

    /**
     * @var integer
     */
    private $state = self::STATE_RUNNING;

    /**
     * {@inheritdoc}
     */
    public function invoke($tasks, $param)
    {
        while (($task = array_shift($tasks)) !== null) {
            $param = call_user_func($task, $param);
        }

        $this->freeze();

        return $param;
    }

    /**
     * {@inheritdoc}
     */
    public function resume()
    {
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
        throw new ProcessException(
            "Current execution task is not interruptible."
        );
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
