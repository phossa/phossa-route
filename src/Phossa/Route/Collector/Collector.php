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

use Phossa\Route\Status;
use Phossa\Route\RouteInterface;
use Phossa\Route\Message\Message;
use Phossa\Route\Regex\ParserGcb;
use Phossa\Route\Regex\ParserInterface;
use Phossa\Route\Context\ResultInterface;
use Phossa\Route\Context\RequestInterface;
use Phossa\Route\Exception\LogicException;
use Phossa\Route\Debug\DebuggableInterface;

/**
 * Collector
 *
 * Regular Expression Routing (RER)
 *
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
class Collector extends CollectorAbstract
{
    /**
     * pattern parser
     *
     * @var    ParserInterface
     * @access protected
     */
    protected $parser;

    /**
     * routes
     *
     * @var    array
     * @access protected
     */
    protected $routes = [];

    /**
     * Constructor
     *
     * @param  ParserInterface $parser
     * @access public
     * @api
     */
    public function __construct(ParserInterface $parser = null)
    {
        $this->parser = $parser ?: new ParserGcb();

        // set debug
        $this->info(
            Message::get(
                Message::DEBUG_ADD_PARSER,
                get_class($this->parser),
                get_class($this)
            )
        );

        // set collector debug mode also
        if ($this->getDebugMode() &&
            $this->parser instanceof DebuggableInterface) {
            $this->parser->setDebugMode(true);
        }
    }

    /**
     * @inheritDoc
     */
    public function addRoute(RouteInterface $route)
    {
        // generate unique key
        $routeKey = $this->getRouteKey($route);

        // related http methods
        $methods  = $route->getMethods();

        // parse pattern if not yet
        if (!isset($this->routes[$routeKey])) {
            $this->routes[$routeKey] = [];
            $this->parser->parse($routeKey, $route->getPattern());
        }

        // save routes
        foreach ($methods as $method) {
            // duplication found
            if (isset($this->routes[$routeKey][$method])) {
                throw new LogicException(
                    Message::get(
                        Message::ROUTE_DUPLICATED,
                        $route->getPattern(),
                        $method
                    ),
                    Message::ROUTE_DUPLICATED
                );
            }
            $this->routes[$routeKey][$method] = $route;
        }

        // debug message
        $this->info(Message::get(
            Message::DEBUG_ADD_ROUTE,
            $route->getPattern(),
            join('|', $methods)
        ));

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function match(ResultInterface $result)/*# : bool */
    {
        $res = $this->parser->match($result->getRequest()->getPathInfo());
        if ($res) {
            list($routeKey, $params) = $res;
            return $this->getRoute($result, $routeKey, $params);
        }
        return false;
    }

    /**
     * Calculate route's unique key
     *
     * @param  RouteInterface $route
     * @return string
     * @access protected
     */
    protected function getRouteKey(RouteInterface $route)/*# : string */
    {
        return 'x' . substr(md5($route->getPattern()), -7);
    }

    /**
     * Get matched route
     *
     * @param  ResultInterface $result
     * @param  string $routeKey unique route key
     * @param  array $matches matched parameters
     * @return bool
     * @access protected
     */
    protected function getRoute(
        ResultInterface $result,
        /*# string */ $routeKey,
        array $matches
    )/*# : bool */ {
        $request = $result->getRequest();
        $method  = $request->getHttpMethod();

        // matched but method not allowed
        if (!isset($this->routes[$routeKey][$method])) {
            $result->setStatus(Status::METHOD_NOT_ALLOWED);
            return false;
        }

        // get the route
        $route = $this->routes[$routeKey][$method];

        // apply others filters
        if (!$this->filtering($route, $request)) {
            $status = Status::PRECONDITION_FAILED;
            $result->setStatus($status)
                   ->setHandler($route->getHandler($status));
            return false;
        }

        // remember the route
        $result->setRoute($route);

        // set result's parameters
        $result->setParameter(array_replace($route->getDefault(), $matches));

        // set status & handler
        $status = Status::OK;
        $result->setStatus($status)
               ->setHandler($route->getHandler(Status::OK));

        return true;
    }

    /**
     * Filtering in route
     *
     * @param  RouteInterface $route
     * @param  RequestInterface $request
     * @return bool
     * @access protected
     */
    protected function filtering(
        RouteInterface $route,
        RequestInterface $request
    ) {
        foreach ($route->getFilters() as $field => $filter) {
            list($grpName, $keyName) = explode('.', $field, 2);
            switch ($grpName) {
                case 'session':
                    $against = isset($_SESSION[$keyName]) ?
                        $_SESSION[$keyName] : '';
                    break;
                case 'cookie':
                    $against = isset($_COOKIE[$keyName]) ?
                        $_COOKIE[$keyName] : '';
                    break;
                case 'request':
                    $against = $request->getRequestInfo($keyName);
                    break;
                default:
                    $against = $request->getServerInfo($keyName);
                    break;
            }

            if (is_callable($filter)) {
                return $filter((string) $against);
            } elseif (!preg_match('~'.$filter.'~x', (string) $against)) {
                return false;
            }
        }
        return true;
    }
}
