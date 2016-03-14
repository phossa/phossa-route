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
 * ResolverAbstract
 *
 * Sample resolver
 *
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
class ResolverAbstract implements ResolverInterface
{
    /**
     * @inheritDoc
     */
    public function resolve($handler)/*# : callable */
    {
        if (is_callable($handler)) {
            return $handler;
        } else {
            return function () use ($handler) {
                echo sprintf(
                    "UNKNOWN HANDLER %s\n",
                    print_r($handler, true)
                );
            };
        }
    }
}
