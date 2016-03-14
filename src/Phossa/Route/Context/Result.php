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

namespace Phossa\Route\Context;

use Phossa\Route\Status;
use Phossa\Route\RouteInterface;

/**
 * Result
 *
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @see     ResultInterface
 * @version 1.0.0
 * @since   1.0.0 added
 */
class Result implements ResultInterface
{
    /**
     * status
     *
     * @var    int
     * @access protected
     */
    protected $status = Status::NOT_FOUND;

    /**
     * parameters
     *
     * @var    string[]
     * @access protected
     */
    protected $parameters = [];

    /**
     * request object
     *
     * @var    RequestInterface
     * @access protected
     */
    protected $request;

    /**
     * the handler
     *
     * @var    callable|array
     * @access protected
     */
    protected $handler;

    /**
     * the matched route
     *
     * @var    RouteInterface
     * @access protected
     */
    protected $route;

    /**
     * constructor
     *
     * @param  RequestInterface $request
     * @access public
     * @api
     */
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
        $this->setParameter($request->getRequestInfo());
    }

    /**
     * @inheritDoc
     */
    public function getStatus()/*# : int */
    {
        return $this->status;
    }

    /**
     * @inheritDoc
     */
    public function setStatus(/*# int */ $status)
    {
        $this->status = (int) $status;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getParameter(/*# string */ $key = '')
    {
        if (empty($key)) {
            return $this->parameters;
        } elseif (isset($this->parameters[$key])) {
            return $this->parameters[$key];
        } else {
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function setParameter($key, /*# string */ $value = '')
    {
        if (is_array($key)) {
            $this->parameters = array_replace($this->parameters, $key);
        } else {
            $this->parameters[(string) $key] = (string) $value;
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRequest()/*# : RequestInterface */
    {
        return $this->request;
    }
    /**
     * @inheritDoc
     */
    public function setHandler($handler)
    {
        if ($handler) {
            $this->handler = $handler;
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @inheritDoc
     */
    public function setRoute(RouteInterface $route)
    {
        $this->route = $route;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRoute()/*# : RouteInterface */
    {
        return $this->route;
    }
}
