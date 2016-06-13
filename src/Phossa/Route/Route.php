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
use Phossa\Route\Exception\LogicException;

/**
 * One matching route
 *
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
class Route implements RouteInterface, Extension\ExtensionAwareInterface
{
    use Handler\HandlerAwareTrait,
        Extension\ExtensionAwareTrait;

    /**#@+
     * Route related extension stages
     *
     * BEFORE_ROUTE: before executing this route's handler
     * AFTER_ROUTE : after handler executed
     *
     * @const
     */
    const BEFORE_ROUTE = 'BEFORE_ROUTE';
    const AFTER_ROUTE  = 'AFTER_ROUTE';
    /**#@-*/

    /**
     * matching pattern for $_SERVER['PATH_INFO']
     *
     * @var    string
     * @access protected
     */
    protected $pattern;

    /**
     * allowed http methods
     *
     * @var    string[]
     * @access protected
     */
    protected $methods;

    /**
     * filters for other request field, such as $_SERVER['SERVER_NAME']
     *
     * @var    string[]
     * @access protected
     */
    protected $filters  = [];

    /**
     * default values for placeholders in the pattern
     *
     * @var    array
     * @access protected
     */
    protected $defaults = [];

    /**
     * Constructor
     *
     * @param  string $httpMethod 'GET|POST' allowed for this route.
     * @param  string $pathPattern matching pattern for $_SERVER['PATH_INFO']
     * @param  callable|array $handler for Status::OK status
     * @param  array $defaultValues default value for placeholders
     * @access public
     * @api
     */
    public function __construct(
        /*# string */ $httpMethod = 'GET',
        /*# string */ $pathPattern = '',
        $handler = null,
        array $defaultValues = []
    ) {
        $this->setMethods($httpMethod)
             ->setPattern($pathPattern)
             ->addHandler(Status::OK, $handler)
             ->setDefault($defaultValues);
    }

    /**
     * {@inheritDoc}
     */
    public function setPattern(/*# string */ $pattern = '')
    {
        // error checking
        if (substr_count($pattern, '[') !== substr_count($pattern, ']') ||
            substr_count($pattern, '{') !== substr_count($pattern, '}')) {
            throw new LogicException(
                Message::get(Message::PATTERN_MALFORMED, $pattern),
                Message::PATTERN_MALFORMED
            );
        }

        // check default values in the pattern
        if (false !== strpos($pattern, '=')) {
            $pattern = $this->extractDefaultValues($pattern);
        }

        $this->pattern = $pattern;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getPattern()/*# : string */
    {
        return $this->pattern;
    }

    /**
     * {@inheritDoc}
     */
    public function setMethods(/*# string */ $methods)
    {
        $this->methods = preg_split(
            '~[^A-Z]+~',
            strtoupper($methods),
            -1,
            PREG_SPLIT_NO_EMPTY
        );
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getMethods()/*# : array */
    {
        return $this->methods;
    }

    /**
     * {@inheritDoc}
     */
    public function addFilter(/*# string */ $field, $filter)
    {
        $this->filters[(string) $field] = $filter;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters()/*# : array */
    {
        return $this->filters;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefault(array $values)
    {
        $this->defaults = array_replace($this->defaults, $values);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getDefault()/*# : array */
    {
        return $this->defaults;
    }

    /**
     * Extract default values from the pattern
     *
     * @param  string $pattern
     * @return string
     * @access protected
     */
    protected function extractDefaultValues(
        /*# string */ $pattern
    )/*# : string */ {
        $regex = '~\{([a-zA-Z][a-zA-Z0-9_]*+)[^\}]*(=[a-zA-Z0-9._]++)\}~';
        if (preg_match_all($regex, $pattern, $matches, \PREG_SET_ORDER)) {
            $srch = $repl = $vals = [];
            foreach ($matches as $m) {
                $srch[] = $m[0];
                $repl[] = str_replace($m[2], '', $m[0]);
                $vals[$m[1]] = substr($m[2],1);
            }
            $this->setDefault($vals);
            return str_replace($srch, $repl, $pattern);
        }
        return $pattern;
    }
}
