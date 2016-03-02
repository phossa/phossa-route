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
 * Request
 *
 * A wrapper of HTTP request for easy access
 *
 * @method string|null server(string $key)  return value in $_SERVER
 * @method string|null request(string $key) return value in $_REQUEST
 *
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
class Request
{
    /**
     * variable fields
     *
     * @var    string[]
     * @access protected
     */
    protected $fields = [
        'request', 'server', 'session', 'cookie', 'post', 'files', 'get'
    ];

    /**
     * variables
     *
     * @var    array
     * @access protected
     */
    protected $vars = [];

    /**
     * get values
     *
     * @param  string $method method name
     * @param  array $arguments method arguments
     * @return string|null
     * @throws void
     * @access public
     * @api
     */
    public function __call(/*# string */ $method, array $arguments)
    {
        if (!isset($this->vars[$method])) {
            return null;
        } elseif (empty($arguments)) {
            return $this->vars[$method];
        } else {
            $key = $method =='server' ? strtoupper($arguments[0]) :
                (string) $arguments[0];
            return isset($this->vars[$method][$key]) ?
                $this->vars[$method][$key] :
                null;
        }
    }

    /**
     * constructor
     *
     * @param  string $url the provided url
     * @param  Request pass another request here
     * @param  array $extraInfo extra request NOT in $url
     * @access public
     * @api
     */
    public function __construct(
        /*# string */ $url = '',
        Request $request = null,
        array $extraInfo = []
    ) {
        $this->setGlobals($request)
             ->parseUrl($url)
             ->setUrlValues($extraInfo);
    }

    /**
     * Set up global variables
     *
     * @param  Request pass another request here
     * @return static
     * @access protected
     */
    protected function setGlobals(Request $request = null)
    {
        if ($request) {
            foreach ($this->fields as $fld) {
                $this->vars[$fld] = $request->$fld();
            }
        } else {
            foreach ($this->fields as $fld) {
                $this->vars[$fld] = ${'_' . strtoupper($fld)};
            }
        }
        return $this;
    }

    /**
     * Parse a given URL
     *
     * @param  string $url the URL
     * @return static
     * @access protected
     */
    protected function parseUrl(/*# string */ $url)
    {
        // server data
        $server = &$this->vars['server'];

        // fix beforehand
        $server['FRAGMENT'] = '';
        $server['SERVER_SCHEME'] = $server['HTTPS'] ? 'https' : 'http';

        if ($url) {
            $parts = parse_url($url);

            // scheme
            if (isset($parts['scheme'])) {
                $server['SERVER_SCHEME'] = $parts['scheme'];
            }

            // host
            if (isset($parts['host'])) {
                $server['HTTP_HOST'] = $parts['host'];
            }

            // port
            if (isset($parts['port'])) {
                $server['SERVER_PORT'] = $parts['port'];
            }

            // fragment
            if (isset($parts['fragment'])) {
                $server['FRAGMENT'] = $parts['fragment'];
            }

            // fix query
            $this->fixUrlQuery($parts);

            // fix path
            $this->fixUrlPath($parts);
        }

        // fix afterwards
        $server['HTTPS'] = $parts['scheme'] !== 'http';

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
        $vars = &$this->vars;

        // always reset
        $vars['server']['QUERY_STRING'] = '';
        $vars['get'] = $vars['post'] = $vars['files'] = $vars['request'] = [];

        // parse query
        if (isset($parts['query'])) {
            $vars['server']['QUERY_STRING'] = $parts['query'];
            $parsed = [];
            parse_str($parts['query'], $parsed);
            $vars['get'] = $parsed;
            $vars['request'] = array_replace($vars['cookie'], $parsed);
        }
    }

    /**
     * fix path in $this->server
     *
     * @param  array $parts parsed url
     * @return void
     * @access protected
     */
    protected function fixUrlPath(array $parts)
    {
        $server = &$this->vars['server'];

        if (isset($parts['path'])) {
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

            $server['REQUEST_URI'] = $parts['path'] .
                empty($server['QUERY_STRING']) ? '' :
                ('?' . $server['QUERY_STRING']) .
                empty($server['FRAGMENT']) ? '' :
                ('#' . $server['FRAGMENT']);
        }
    }

    /**
     * Setup values
     *
     * @param  array $data values to set
     * @return static
     * @access protected
     */
    protected function setUrlValues(array $data)
    {
        if (count($data)) {
            foreach ($data as $key => $val) {
                if (isset($this->vars[$key])) {
                    foreach ($val as $k => $v) {
                        $this->vars[$key][$k] = $v;
                    }
                } else {

                }
            }
        }
        return $this;
    }
}
