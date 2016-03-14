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

/**
 * RequestInterface
 *
 * @interface
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
interface RequestInterface
{
    /**
     * Get HTTP method
     *
     * @return string
     * @access public
     * @api
     */
    public function getHttpMethod()/*# : string */;

    /**
     * Get PATH_INFO
     *
     * @return string
     * @access public
     * @api
     */
    public function getPathInfo()/*# : string */;

    /**
     * Get info from $_SERVER
     *
     * @param  string $key
     * @return string|array|null
     * @access public
     * @api
     */
    public function getServerInfo(/*# string */ $key = '');

    /**
     * Get info from $_REQUEST
     *
     * @param  string $key
     * @return string|array|null
     * @access public
     * @api
     */
    public function getRequestInfo(/*# string */ $key = '');
}
