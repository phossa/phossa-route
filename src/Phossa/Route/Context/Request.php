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

/**
 * Request wrapper
 *
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @see     RequestInterface
 * @version 1.0.0
 * @since   1.0.0 added
 */
class Request implements RequestInterface
{
    /**
     * server info from $_SERVER
     *
     * @var    string[]
     * @access protected
     */
    protected $server = [];

    /**
     * request info from $_REQUEST
     *
     * @var    string[]
     * @access protected
     */
    protected $request = [];

    /**
     * constructor
     *
     * @param  string HTTP method
     * @param  string $url URL
     * @access public
     * @api
     */
    public function __construct(
        /*# string */ $httpMethod = '',
        /*# string */ $url = ''
    ) {
        $this->parseUrl((string) $httpMethod, (string) $url);
    }

    /**
     * @inheritDoc
     */
    public function getHttpMethod()/*# : string */
    {
        return $this->getInfo('request_method', 'server');
    }

    /**
     * @inheritDoc
     */
    public function getPathInfo()/*# : string */
    {
        return $this->getInfo('path_info', 'server');
    }

    /**
     * @inheritDoc
     */
    public function getServerInfo(/*# string */ $key = '')
    {
        return $this->getInfo((string) $key, 'server');
    }

    /**
     * @inheritDoc
     */
    public function getRequestInfo(/*# string */ $key = '')
    {
        return $this->getInfo((string) $key, 'request');
    }

    /**
     * Get info
     *
     * @param  string $key
     * @param  string $method
     * @return string|array|null
     * @access protected
     */
    protected function getInfo(
        /*# string */ $key,
        /*# string */ $method = 'server'
    ) {
        if ('server' === $method) {
            $key = strtoupper($key);
        }
        $data = $this->$method;

        if (empty($key)) {
            return $data;
        } elseif (isset($data[$key])) {
            return $data[$key];
        } else {
            return null;
        }
    }

    /**
     * Parse a given URL
     *
     * @param  string $httpMethod HTTP method
     * @param  string $url URL
     * @param  array $requestData simulate post data etc.
     * @return status
     * @access protected
     */
    protected function parseUrl(
        /*# string */ $httpMethod,
        /*# string */ $url
    ) {
        $this->server = $_SERVER;
        $server = &$this->server;

        // set http method
        if ($httpMethod) {
            $server['REQUEST_METHOD'] = $httpMethod;
        } elseif (!isset($server['REQUEST_METHOD'])) {
            $server['REQUEST_METHOD'] = 'GET';
        }

        // parse url
        if (empty($url)) {
            $this->request = $_REQUEST;
        } else {
            $parts = parse_url($url);

            // scheme
            if (isset($parts['scheme'])) {
                $scheme = strtolower($parts['scheme']);
                $server['HTTPS'] = 'https' === $scheme ? $scheme : '';
            }

            // host
            if (isset($parts['host'])) {
                $server['HTTP_HOST'] = $parts['host'];
            }

            // port
            if (isset($parts['port'])) {
                $server['SERVER_PORT'] = $parts['port'];
            }

            // fix query
            $this->fixUrlQuery($parts);

            // fix path
            $this->fixUrlPath($parts);
        }

        // normalize
        $server['PATH_INFO'] = '/' . trim($server['PATH_INFO'], '/');

        return $this;
    }

    /**
     * fix query in $this->server
     *
     * @param  array $parts parsed url
     * @return void
     * @access protected
     */
    protected function fixUrlQuery(array $parts)
    {
        $server = &$this->server;

        // reset
        $server['QUERY_STRING'] = '';

        // parse query string
        $get = [];
        if (isset($parts['query'])) {
            $server['QUERY_STRING'] = $parts['query'];
            parse_str($parts['query'], $get);
        }

        // request
        $this->request = $_COOKIE;
        switch ($server['REQUEST_METHOD']) {
            case 'POST':
                $this->request = array_replace($this->request, $_POST, $get);
                break;
            default:
                $this->request = array_replace($this->request, $get);
                break;
        }
    }

    /**
     * fix path in $this->server
     *
     * @param  array &$parts parsed url
     * @return void
     * @access protected
     */
    protected function fixUrlPath(array &$parts)
    {
        $server = &$this->server;
        if (isset($parts['path'])) {
            $parts['path'] = rawurldecode($parts['path']);

            // fix PATH_INFO
            if (isset($server['PATH_INFO'])) {
                $uri = explode(
                    $server['PATH_INFO'],
                    $server['REQUEST_URI']
                );
                $server['PATH_INFO'] =
                    str_replace($uri[0], '', $parts['path']);
            } else {
                $server['PATH_INFO'] = $parts['path'];
            }

            // fix REQUEST_URI
            $server['REQUEST_URI'] = $parts['path'] .
                (
                    empty($server['QUERY_STRING']) ? '' :
                    ('?' . $server['QUERY_STRING'])
                );
        }
    }
}
