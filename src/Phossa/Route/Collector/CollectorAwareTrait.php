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
 * CollectorAwareTrait
 *
 * @trait
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @see     CollectorAwareInterface
 * @version 1.0.0
 * @since   1.0.0 added
 */
trait CollectorAwareTrait
{
    /**
     * collector pool
     *
     * @var    CollectorInterface[]
     * @access protected
     */
    protected $collectors = [];

    /**
     * @inheritDoc
     */
    public function addCollector(CollectorInterface $collector)
    {
        $this->collectors[] = $collector;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCollectors()/*# : array */
    {
        return $this->collectors;
    }
}
