<?php
/**
 * Phossa Project
 *
 * PHP version 5.4
 *
 * @category  Library
 * @package   Phossa\Route
 * @author    Hong Zhang <phossa@126.com>
 * @copyright 2015 phossa.com
 * @license   http://mit-license.org/ MIT License
 * @link      http://www.phossa.com/
 */
/*# declare(strict_types=1); */

namespace Phossa\Route\Debug;

use Psr\Log\LoggerInterface;

/**
 * DebuggableTrait
 *
 * @trait
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @see     DebuggableInterface
 * @version 1.0.0
 * @since   1.0.0 added
 */
trait DebuggableTrait
{
    /**
     * debug mode
     *
     * @var    bool
     * @access protected
     */
    protected $debug_mode = false;

    /**
     * debugger
     *
     * @var    LoggerInterface
     * @access protected
     */
    protected $debugger;

    /**
     * @inheritDoc
     */
    public function setDebugger(LoggerInterface $logger)
    {
        $this->debugger = $logger;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDebugMode(/*# bool */ $status = true)
    {
        $this->debug_mode = (bool) $status;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDebugMode()/*# : bool */
    {
        return $this->debug_mode;
    }

    /**
     * @inheritDoc
     */
    public function debug(/*# string */ $message)
    {
        return $this->log('debug', $message);
    }

    /**
     * @inheritDoc
     */
    public function info(/*# string */ $message)
    {
        return $this->log('info', $message);
    }

    /**
     * log message
     *
     * @param  string $level log level
     * @param  string $message message to log
     * @return static
     * @access protected
     */
    protected function log(/*# string */ $level, /*# string */ $message)
    {
        if ($this->debug_mode) {
            $this->debugger->log(
                $level,
                $message,
                [ 'className' => get_class($this) ]
            );
        }
        return $this;
    }
}
