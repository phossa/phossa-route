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
 * DebuggableInterface
 *
 * @interface
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
interface DebuggableInterface
{
    /**
     * Set debugger
     *
     * @param  LoggerInterface $logger
     * @return static
     * @access public
     * @api
     */
    public function setDebugger(LoggerInterface $logger);

    /**
     * Set debugging status
     *
     * @param  bool $status
     * @return static
     * @access public
     * @api
     */
    public function setDebugMode(/*# bool */ $status = true);

    /**
     * Get debugging status
     *
     * @return bool
     * @access public
     * @internal
     */
    public function getDebugMode()/*# : bool */;

    /**
     * spit debugging message
     *
     * @param  string $message message to send out
     * @return static
     * @access public
     * @internal
     */
    public function debug(/*# string */ $message);

    /**
     * spit info message
     *
     * @param  string $message message to send out
     * @return static
     * @access public
     * @internal
     */
    public function info(/*# string */ $message);
}
