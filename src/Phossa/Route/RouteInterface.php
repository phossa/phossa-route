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

namespace Phossa\Route;

use Phossa\Route\Exception\LogicException;

/**
 * RouteInterface
 *
 * @interface
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
interface RouteInterface extends Handler\HandlerAwareInterface
{
    /**
     * Set $_SERVER['PATH_INFO'] matching pattern
     *
     * @param  string $pattern pattern to match
     * @return self
     * @throws LogicException if pattern malformed
     * @access public
     * @internal
     */
    public function setPattern(/*# string */ $pattern = '');

    /**
     * Get $_SERVER['PATH_INFO'] matching pattern
     *
     * @return string
     * @access public
     * @api
     */
    public function getPattern()/*# : string */;

    /**
     * Set route http methods allowed like 'GET|HEAD|POST'
     *
     * @param  string $methods method to match
     * @return self
     * @access public
     * @internal
     */
    public function setMethods(/*# string */ $methods);

    /**
     * Get allowed methods in array
     *
     * @return array
     * @access public
     * @api
     */
    public function getMethods()/*# : array */;

    /**
     * Add request filters
     *
     * User can match $_SERVER['SERVER_NAME'] field by
     *
     * ```php
     * $route->addFilter('server.server_name', '(m|www).phossa.com');
     * ```
     *
     * @param  string $field something like 'server.server_name'
     * @param  string|callable $filter regex pattern or callable
     * @return self
     * @access public
     * @api
     */
    public function addFilter(/*# string */ $field, $filter);

    /**
     * Get all filters
     *
     * @return array
     * @access public
     * @internal
     */
    public function getFilters()/*# : array */;

    /**
     * Set default values for placeholders/parameters
     *
     * @param  array $values default values
     * @return self
     * @access public
     * @internal
     */
    public function setDefault(array $values);

    /**
     * Get default values for placeholders
     *
     * @return array
     * @access public
     * @internal
     */
    public function getDefault()/*# : array */;
}
