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

use Phossa\Route\Route;
use Phossa\Route\RouteInterface;
use Phossa\Route\Context\ResultInterface;
use Phossa\Route\Extension\ExtensionAwareInterface;

/**
 * CollectorAbstract
 *
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @see     CollectorInterface
 * @version 1.0.0
 * @since   1.0.0 added
 */
abstract class CollectorAbstract implements CollectorInterface, ExtensionAwareInterface
{
    use \Phossa\Route\Extension\ExtensionAwareTrait;

    /**#@+
     * Collector level extension stages
     *
     * BEFORE_COLL: before matching routes in this collector
     * AFTER_COLL : after a successful matching
     *
     * @const
     */
    const BEFORE_COLL = 'BEFORE_COLL';
    const AFTER_COLL  = 'AFTER_COLL';
    /**#@-*/

    /**
     * @inheritDoc
     */
    abstract public function addRoute(RouteInterface $route);

    /**
     * @inheritDoc
     */
    public function matchRoute(ResultInterface $result)/*# : bool */
    {
        if ($this->runExtensions(self::BEFORE_COLL, $result) &&
            $this->match($result) &&
            $this->runExtensions(self::AFTER_COLL, $result)
        ) {
            return true;
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function addGet(
        /*# string */ $pathPattern,
        $handler = null,
        array $defaultValues = []
    ) {
        return $this->addRoute(
            new Route('GET', $pathPattern, $handler, $defaultValues)
        );
    }

    /**
     * @inheritDoc
     */
    public function addPost(
        /*# string */ $pathPattern,
        $handler = null,
        array $defaultValues = []
    ) {
        return $this->addRoute(
            new Route('POST', $pathPattern, $handler, $defaultValues)
        );
    }

    /**
     * Child class must implement this method
     *
     * MUST set $result status and handler in this method
     *
     * @param  ResultInterface $result result object
     * @return bool
     * @access protected
     */
    abstract protected function match(ResultInterface $result)/*# : bool */;
}
