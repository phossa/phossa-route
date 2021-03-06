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

namespace Phossa\Route\Handler;

use Phossa\Route\Message\Message;
use Phossa\Route\Debug\DebuggableInterface;

/**
 * HandlerAwareTrait
 *
 * Implementation of HandlerAwareInterface
 *
 * @trait
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @see     HandlerAwareInterface
 * @version 1.0.0
 * @since   1.0.0 added
 */
trait HandlerAwareTrait
{
    /**
     * Different handler for different status.
     *
     * usually only the Status::OK status handler is set.
     *
     * @var    array
     * @access protected
     */
    protected $handlers = [];

    /**
     * {@inheritDoc}
     */
    public function addHandler(/*# int */ $status, $handler = null)
    {
        if (!is_null($handler)) {
            $this->handlers[(int) $status] = $handler;

            // debug message
            if ($this instanceof DebuggableInterface) {
                $this->debug(Message::get(
                    Message::DEBUG_ADD_HANDLER,
                    is_object($handler) ? get_class($handler) : gettype($handler),
                    get_class($this)
                ));
            }
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getHandler(/*# int */ $status)
    {
        if (isset($this->handlers[$status])) {
            return $this->handlers[$status];
        }
        return null;
    }
}
