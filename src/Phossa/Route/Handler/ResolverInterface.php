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

namespace Phossa\Route\Handler;

use Phossa\Route\Exception\LogicException;

/**
 * ResolverInterface
 *
 * Resolve handler to callable
 *
 * @interface
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
interface ResolverInterface
{
    /**
     * Resolve the given handler
     *
     * @param  mixed $handler the given handler
     * @return callable
     * @throws LogicException if resolving failed
     * @access public
     * @internal
     */
    public function resolve($handler)/*# : callable */;
}
