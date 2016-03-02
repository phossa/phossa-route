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

/**
 * Dispatcher
 *
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
class Dispatcher
{
    /**#@+
     * Routing return code (similar to HTTP codes)
     *
     * @const
     */
    const OK                    = 200;
    const MOVED_PERMANENTLY     = 301;
    const NOT_MODIFIED          = 304;
    const BAD_REQUEST           = 400;
    const UNAUTHORIZED          = 401;
    const NOT_FOUND             = 403;
    const METHOD_NOT_ALLOWED    = 405;
    const SERVICE_UNAVAILABLE   = 503;

    /**#@-*/

}
