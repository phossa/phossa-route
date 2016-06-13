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

/**
 * ResolverAbstract
 *
 * Sample resolver
 *
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @see     ResolverInterface
 * @version 1.0.2
 * @since   1.0.0 added
 * @since   1.0.2 added Controller/Action part
 */
class ResolverAbstract implements ResolverInterface
{
    /**
     * {@inheritDoc}
     */
    public function resolve($handler)/*# : callable */
    {
        // callable
        if (is_callable($handler)) {
            return $handler;

        // append Controller/Action 1.0.2
        } elseif (is_array($handler)) {
            $controller = $handler[0] . 'Controller';
            $action = $handler[1] . 'Action';
            $h = [$controller, $action];
            if (is_callable($h)) {
                return $h;
            }
        }

        // unknown type
        return function () use ($handler) {
            echo sprintf(
                "UNKNOWN HANDLER %s\n",
                print_r($handler, true)
            );
        };
    }
}
