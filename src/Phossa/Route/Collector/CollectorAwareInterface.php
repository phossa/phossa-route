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

/**
 * CollectorAwareInterface
 *
 * @interface
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
interface CollectorAwareInterface
{
    /**
     * Inject a collector
     *
     * @param  CollectorInterface $collector
     * @return self
     * @access public
     * @api
     */
    public function addCollector(CollectorInterface $collector);

    /**
     * Get all collectors
     *
     * @return CollectorInterface[]
     * @access public
     * @internal
     */
    public function getCollectors()/*# : array */;
}
