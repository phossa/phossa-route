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

use Phossa\Route\Message\Message;
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
class Dispatcher implements DispatcherInterface, Handler\HandlerAwareInterface, Extension\ExtensionAwareInterface, Collector\CollectorAwareInterface, Debug\DebuggableInterface, Collector\AddRouteInterface
{
    use Debug\DebuggableTrait,
        Handler\HandlerAwareTrait,
        Extension\ExtensionAwareTrait,
        Collector\CollectorAwareTrait;

    /**#@+
     * Dispatcher related extension stages
     *
     * @const
     */

    // before any matching starts
    const BEFORE_MATCH      = 'BEFORE_MATCH';

    // after a successful matching
    const AFTER_MATCH       = 'AFTER_MATCH';

    // after a successful matching, before execute handler
    const BEFORE_DISPATCH   = 'BEFORE_DISPATCH';

    // after handler executed successfully
    const AFTER_DISPATCH    = 'AFTER_DISPATCH';

    // before execute dispatcher's default handler
    const BEFORE_DEFAULT    = 'BEFORE_DEFAULT';

    // after dispatcher's default handler executed
    const AFTER_DEFAULT     = 'AFTER_DEFAULT';

    /**#@-*/

    /**
     * Result
     *
     * @var    Context\ResultInterface
     * @access protected
     */
    protected $result;

    /**
     * resolver
     *
     * @var    Handler\ResolverInterface
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
     * @param  Handler\ResolverInterface $resolver
     * @access public
     * @api
     */
    public function __construct(
        CollectorInterface $collector = null,
        Handler\ResolverInterface $resolver = null
    ) {
        $this->addCollector($collector ?: new Collector\Collector());
        $this->resolver = $resolver ?: new Handler\ResolverAbstract();
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
     * @inheritDoc
     */
    public function getResult()/*# : Context\ResultInterface */
    {
        return $this->result;
    }

    /**
     * Add route to the first collector
     *
     * {@inheritDoc}
     */
    public function addRoute(RouteInterface $route)
    {
        $this->getCollectors()[0]->addRoute($route);
        return $this;
    }

    /**
     * Add a 'GET,HEAD' route to the first collector
     *
     * {@inheritDoc}
     */
    public function addGet(
        /*# string */ $pathPattern,
        $handler = null,
        array $defaultValues = []
    ) {
        $this->getCollectors()[0]->addGet(
            $pathPattern,
            $handler,
            $defaultValues
        );
        return $this;
    }

    /**
     * Add a 'POST' route to the first collector
     *
     * {@inheritDoc}
     */
    public function addPost(
        /*# string */ $pathPattern,
        $handler = null,
        array $defaultValues = []
    ) {
        $this->getCollectors()[0]->addPost(
            $pathPattern,
            $handler,
            $defaultValues
        );
        return $this;
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

        if (is_null($handler)) {
            // matched, but no handler found
            return $this->defaultHandler();
        } else {
            $callable = $this->resolver->resolve($handler);

            // extension ?
            if (($route = $this->result->getRoute()) && $route->hasExtension()) {
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
     * Execute dispatcher's default handler
     *
     * @return false
     * @access protected
     */
    protected function defaultHandler()
    {
        if ($this->runExtensions(self::BEFORE_DEFAULT, $this->result)) {
            $status  = $this->result->getStatus();
            $handler = $this->getHandler($status);
            if ($handler) {
                $handler($this->result);
            } else {
                echo Message::get(Message::DEBUG_NEED_HANDLER, $status);
            }
            $this->runExtensions(self::AFTER_DEFAULT, $this->result);
        }
        return false;
    }
}
