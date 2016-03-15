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

/**
 * HandlerAwareInterface
 *
 * @interface
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
interface HandlerAwareInterface
{
    /**
     * Add route handler for this status
     *
     * @param  int $status status when execute this handler
     * @param  callable|array $handler callable or ['controller','method']
     * @return static
     * @access public
     * @api
     */
    public function addHandler(/*# int */ $status, $handler = null);

    /**
     * Get route handler relate to the specific status code
     *
     * @param  int $status result status code
     * @return callable|null
     * @access public
     * @internal
     */
    public function getHandler(/*# int */ $status);
}
