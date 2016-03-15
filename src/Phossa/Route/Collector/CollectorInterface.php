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

use Phossa\Route\Context\ResultInterface;

/**
 * CollectorInterface
 *
 * Route collector interface
 *
 * @interface
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
interface CollectorInterface extends AddRouteInterface
{
    /**
     * Match with routes
     *
     * @param  ResultInterface $result result object
     * @return bool
     * @access public
     * @internal
     */
    public function matchRoute(ResultInterface $result)/*# : bool */;
}
