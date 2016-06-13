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

namespace Phossa\Route\Context;

use Phossa\Route\RouteInterface;

/**
 * ResultInterface
 *
 * @interface
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
interface ResultInterface
{
    /**
     * Get status code
     *
     * @return int
     * @access public
     * @api
     */
    public function getStatus()/*# : int */;

    /**
     * Set status code
     *
     * @param  int $status
     * @return self
     * @access public
     * @api
     */
    public function setStatus(/*# int */ $status);

    /**
     * Get parsed parameters
     *
     * @param  string $key parameter key/name
     * @return string|array|null
     * @access public
     * @api
     */
    public function getParameter(/*# string */ $key = '');

    /**
     * Set parameters
     *
     * @param  string|array $key parameter key/name or parameter array
     * @param  string $value value to set
     * @return self
     * @access public
     * @api
     */
    public function setParameter($key, /*# string */ $value = '');

    /**
     * Get the request object
     *
     * @return RequestInterface
     * @access public
     * @api
     */
    public function getRequest()/*# : RequestInterface */;

    /**
     * Set handler
     *
     * @param  callable|array $handler
     * @return self
     * @access public
     * @api
     */
    public function setHandler($handler);

    /**
     * Get the handler (or pseudo callable)
     *
     * @return callable|array
     * @access public
     * @internal
     */
    public function getHandler();

    /**
     * Set the matched route
     *
     * @param  RouteInterface $route
     * @return self
     * @access public
     * @internal
     */
    public function setRoute(RouteInterface $route);

    /**
     * Get matched route
     *
     * @return RouteInterface
     * @access public
     * @internal
     */
    public function getRoute()/*# : RouteInterface */;
}
