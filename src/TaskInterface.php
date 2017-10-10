<?php

namespace Gandung\Pipeline;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
interface TaskInterface
{
    /**
     * Invoke the instance with given parameter
     *
     * @param mixed $param
     */
    public function __invoke($param);
}
