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
    /**#@+
     * Routing result code (similar to HTTP codes)
     *
     * @const
     */
    const OK                    = 200;
    const MOVED_PERMANENTLY     = 301;
    const NOT_MODIFIED          = 304;
    const BAD_REQUEST           = 400;
    const UNAUTHORIZED          = 401;
    const NOT_FOUND             = 404;
    const METHOD_NOT_ALLOWED    = 405;
    const PRECONDITION_FAILED   = 412;
    const SERVER_ERROR          = 500;
    const SERVICE_UNAVAILABLE   = 503;
    /**#@-*/

    /**#@+
     * Predefined parameter keys
     *
     * @const
     */
    const REDIRECT_URL          = '__redirect_url__';

    /**#@-*/

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
     * @return static
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
     * @return static
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
     * @return static
     * @access public
     * @api
     */
    public function setHandler($handler);

    /**
     * Get the handler (or pseudo callable)
     *
     * @return callable|array
     * @access public
     * @api
     */
    public function getHandler();

    /**
     * Set the matched route
     *
     * @param  RouteInterface $route
     * @return static
     * @access public
     * @api
     */
    public function setRoute(RouteInterface $route);

    /**
     * Get matched route
     *
     * @return RouteInterface
     * @access public
     * @api
     */
    public function getRoute()/*# : RouteInterface */;
}
