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
use Phossa\Route\Collector\CollectorInterface;

/**
 * Dispatcher
 *
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @see     DispatcherInterface
 * @see     Extension\ExtensionAwareInterface
 * @version 1.0.0
 * @since   1.0.0 added
 */
class Dispatcher implements DispatcherInterface, Extension\ExtensionAwareInterface, Collector\CollectorAwareInterface
{
    use Extension\ExtensionAwareTrait,
        Collector\CollectorAwareTrait;

    /**#@+
     * Dispatcher related extension stages
     *
     * BEFORE_MATCH: before matching begins
     * BEFORE_DISPATCH: before executing handler
     * AFTER_MATCH: after successful matching a route
     * AFTER_DISPATCH: after handler executed
     *
     * @const
     */
    const BEFORE_MATCH      = 'BEFORE_MATCH';
    const AFTER_MATCH       = 'AFTER_MATCH';
    const BEFORE_DISPATCH   = 'BEFORE_DISPATCH';
    const AFTER_DISPATCH    = 'AFTER_DISPATCH';
    /**#@-*/

    /**
     * Default handlers for different status
     *
     * @var    array
     * @access protected
     */
    protected $handlers = [];

    /**
     * Result
     *
     * @var    Context\Result
     * @access protected
     */
    protected $result;

    /**
     * resolver
     *
     * @var    ResolverInterface
     * @access protected
     */
    protected $resolver;

    /**
     * constructor
     *
     * Inject a route collector. Extra collectors can be added later with
     * `addCollector()`
     *
     * @param  CollectorInterface $collector
     * @param  ResolverInterface $resolver
     * @access public
     * @api
     */
    public function __construct(
        CollectorInterface $collector,
        ResolverInterface $resolver
    ) {
        $this->addCollector($collector);
        $this->resolver = $resolver;
    }

    /**
     * @inheritDoc
     */
    public function match(ResultInterface $result = null)/*# : bool */
    {
        $this->result = $result ?: $this->init();
        if ($this->runExtensions(self::BEFORE_MATCH, $this->result) &&
            $this->matchRoute() &&
            $this->runExtensions(self::AFTER_MATCH, $this->result)
        ) {
            return true;
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function dispatch(ResultInterface $result = null)/*# : bool */
    {
        if ($this->match($result)) {
            // matched, try dispatcher extension & execute handler
            if ($this->runExtensions(self::BEFORE_DISPATCH, $this->result) &&
                $this->executeHandler() &&
                $this->runExtensions(self::AFTER_DISPATCH, $this->result)
            ) {
                return true;
            } else {
                return false;
            }
        } else {
            // not match, try default handler if any
            $this->defaultHandler();
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function matchUrl(
        /*# string */ $httpMethod = '',
        /*# string */ $url = ''
    )/*# : bool */ {
        return $this->match($this->init($httpMethod, $url));
    }

    /**
     * @inheritDoc
     */
    public function dispatchUrl(
        /*# string */ $httpMethod = '',
        /*# string */ $url = ''
    )/*# : bool */ {
        return $this->dispatch($this->init($httpMethod, $url));
    }

    /**
     * Match routes in collectors
     *
     * @return bool
     * @access protected
     */
    protected function matchRoute()/*# : bool */
    {
        foreach ($this->getCollectors() as $coll) {
            if ($coll->matchRoute($this->result)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function setHandler(/*# int */ $status, $handler)
    {
        $this->handlers[(int) $status] = $this->resolver->resolve($handler);
        return $this;
    }

    /**
     * Init request and result by given url and http method
     *
     * @param  string $httpMethod
     * @param  string $url
     * @return Context\ResultInterface
     * @access protected
     */
    protected function init(
        /*# string */ $httpMethod = '',
        /*# string */ $url = ''
    )/*# Context\ResultInterface */ {
        return new Context\Result(
            new Context\Request($httpMethod, $url)
        );
    }

    /**
     * Execute handler from result
     *
     * @return bool
     * @access protected
     */
    protected function executeHandler()/*# : bool */
    {
        // search handler
        $handler = $this->result->getHandler();
        if (is_null($handler) && ($route = $this->result->getRoute())) {
            $handler = $route->getHandler($this->result->getStatus());
        }

        if (is_null($handler)) {
            // matched, but no handler found
            $this->defaultHandler();
            return false;
        } else {
            $callable = $this->resolver->resolve($handler);

            // extension loaded?
            if (isset($route) && is_object($route) &&
                $route instanceof Extension\ExtensionAwareInterface &&
                $route->hasExtension()
            ) {
                if ($route->runExtensions(Route::BEFORE_ROUTE, $this->result)) {
                    $callable($this->result);
                    $route->runExtensions(Route::AFTER_ROUTE, $this->result);
                }
            } else {
                $callable($this->result);
            }
            return true;
        }
    }

    /**
     * Execute default handler if no handler found
     *
     * @return void
     * @access protected
     */
    protected function defaultHandler()
    {
        $status = $this->result->getStatus();
        if (isset($this->handlers[$status])) {
            $this->handlers[$status]($this->result);
        } else {
            echo sprintf("%d, YOU NEED A REAL HANDLER!", $status);
        }
    }
}
