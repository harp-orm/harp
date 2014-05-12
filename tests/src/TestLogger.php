<?php

namespace CL\Luna\Test;

use Psr\Log\AbstractLogger;
use CL\Atlas\Compiler\Compiler;

/**
 * A dummy logger used for testing
 */
class TestLogger extends AbstractLogger
{
    /**
     * @var array
     */
    protected $entries = array();

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        $this->entries []= isset($context['parameters']) ? Compiler::humanize($message, $context['parameters']) : $message;
    }

    /**
     * @return array
     */
    public function getEntries()
    {
        return $this->entries;
    }
}
