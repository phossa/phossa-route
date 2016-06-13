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

namespace Phossa\Route\Collector;

use Phossa\Route\RouteInterface;
use Phossa\Route\Exception\LogicException;

/**
 * AddRouteInterface
 *
 * Add routes to the collector
 *
 * @interface
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.2
 * @since   1.0.0 added
 * @since   1.0.2 added loadRoute()
 */
interface AddRouteInterface
{
    /**
     * Add one route to the collector
     *
     * @param  RouteInterface $route
     * @return self
     * @throws LogicException if route goes wrong
     * @access public
     * @api
     */
    public function addRoute(RouteInterface $route);

    /**
     * Add multiple routes from file or array, format is
     *
     * ```php
     * return [
     *     '/user/{action:xd}/{id:d}' => [
     *         ['collecor', 'action'],   // handler
     *         'GET,POST',               // methods
     *         ['id' => 1]               // defaults
     *     ],
     *     ...
     * ];
     * ```
     *
     * @param  string|array $fileOrArray
     * @return self
     * @throws LogicException if route goes wrong
     * @access public
     * @api
     */
    public function loadRoute($fileOrArray);

    /**
     * Add a 'GET,HEAD' route
     *
     * @param  string $pathPattern url pattern
     * @param  callable|array $handler
     * @param  array $defaultValues default values for placeholders
     * @return self
     * @access public
     * @api
     */
    public function addGet(
        /*# string */ $pathPattern,
        $handler = null,
        array $defaultValues = []
    );

    /**
     * Add a 'POST' route
     *
     * @param  string $pathPattern url pattern
     * @param  callable|array $handler
     * @param  array $defaultValues default values for placeholders
     * @return self
     * @access public
     * @api
     */
    public function addPost(
        /*# string */ $pathPattern,
        $handler = null,
        array $defaultValues = []
    );
}
