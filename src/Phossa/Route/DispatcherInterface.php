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

use Phossa\Route\Context\ResultInterface;

/**
 * DispatcherInterface
 *
 * @interface
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
interface DispatcherInterface
{
    /**
     * Match against a result object
     *
     * @param  ResultInterface $result result object
     * @return bool
     * @access public
     * @api
     */
    public function match(ResultInterface $result = null)/*# : bool */;

    /**
     * Match and dispatch against a result object
     *
     * @param  ResultInterface $result result object
     * @return bool
     * @access public
     * @api
     */
    public function dispatch(ResultInterface $result = null)/*# : bool */;

    /**
     * Match against URL and HTTP method
     *
     * @param  string $httpMethod if empty will use values from $_SERVER
     * @param  string $url if empty will fetch from $_SERVER
     * @return bool
     * @access public
     * @api
     */
    public function matchUrl(
        /*# string */ $httpMethod = '',
        /*# string */ $url = ''
    )/*# : bool */;

    /**
     * Match and dispatch against URL
     *
     * @param  string $httpMethod if empty will use values from $_SERVER
     * @param  string $url if empty will fetch from $_SERVER
     * @return bool
     * @access public
     * @api
     */
    public function dispatchUrl(
        /*# string */ $httpMethod = '',
        /*# string */ $url = ''
    )/*# : bool */;

    /**
     * Get the result object
     *
     * @return Context\ResultInterface
     * @access public
     * @api
     */
    public function getResult()/*# : Context\ResultInterface */;
}
