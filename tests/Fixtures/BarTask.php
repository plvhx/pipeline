<?php

namespace Gandung\Pipeline\Tests\Fixtures;

use Gandung\Pipeline\TaskInterface;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
class BarTask implements TaskInterface
{
    /**
     * {@inheritdoc}
     */
    public function __invoke($param)
    {
        return join(' ', [$param, 'bar']);
    }
}
