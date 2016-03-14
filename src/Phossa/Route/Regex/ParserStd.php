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

namespace Phossa\Route\Regex;

/**
 * ParserStd
 *
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
class ParserStd extends ParserAbstract
{
    /**
     * new route added ? (for caching purpose)
     *
     * @var    bool
     * @access protected
     */
    protected $modified = false;

    /**
     * regex pool
     *
     * @var    string[]
     * @access protected
     */
    protected $regex = [];

    /**
     * chunk size 4 - 12 for merging regex
     *
     * @var    int
     * @access protected
     */
    protected $chunk = 8;

    /**
     * combined regex (cache)
     *
     * @var    string[]
     * @access protected
     */
    protected $data  = [];

    /**
     * @inheritDoc
     */
    public function parse(
        /*# string */ $name,
        /*# string */ $pattern
    )/*# : string */ {
        $regex = $this->convert($name, $pattern);
        $this->regex[]  = $regex;
        $this->modified = true;
        return $regex;
    }

    /**
     * @inheritDoc
     */
    public function match(/*# string */ $url)
    {
        $matches = [];
        foreach ($this->getRegexData() as $regex) {
            if (preg_match($regex, $url, $matches)) {
                return $this->fixMatches($matches);
            }
        }
        return false;
    }

    /**
     * Convert to regex
     *
     * @param  string $name regex name
     * @param  string $pattern pattern to parse
     * @return static
     * @access protected
     */
    protected function convert(
        /*# string */ $name,
        /*# string */ $pattern
    )/*# : string */ {
        // regex
        $groupname   = "\s*([a-zA-Z][a-zA-Z0-9_]*)\s*";
        $grouptype   = ":\s*([^{}]*(?:\{(?-1)\}[^{}]*)*)";
        $placeholder = sprintf("\{%s(?:%s)?\}", $groupname, $grouptype);
        $segmenttype = "[^/]++";

        $result = preg_replace([
                '~' . $placeholder . '(*SKIP)(*FAIL) | \[~x',
                '~' . $placeholder . '(*SKIP)(*FAIL) | \]~x',
                '~\{' . $groupname . '\}~x',
                '~' . $placeholder . '~x',
            ], [
                '(?:',  // replace '['
                ')?',   // replace ']'
                '{\\1:' . $segmenttype . '}',   // add segementtype
                '(?<${1}'. $name . '>${2})'   // replace groupname
            ], strtr('/' . trim($pattern, '/'), $this->shortcuts)
        );

        return empty($name) ? $result : ("(?<$name>" . $result . ")");
    }

    /**
     * Merge several (chunk size) regex into one
     *
     * @return array
     * @access protected
     * @todo   cache support
     */
    protected function getRegexData()/*# : array */
    {
        // load from cache
        if (empty($this->regex) || !$this->modified) {
            return $this->data;
        }

        // chunk size
        $this->data = array_chunk($this->regex, $this->chunk);

        // join in chunks
        foreach ($this->data as $i => $reg) {
            $this->data[$i] = '~^(?:' . implode('|', $reg) . ')$~x';
        }

        // save to cache here
        $this->modified = false;

        return $this->data;
    }

    /**
     * Fix matched placeholders, return with unique route key
     *
     * @param  array $matches desc
     * @return array [ $name, $matches ]
     * @access protected
     */
    protected function fixMatches($matches)/*# : array */
    {
        // remove numeric keys and empty group match
        foreach ($matches as $idx => $val) {
            if (is_int($idx) || '' === $val) {
                unset($matches[$idx]);
            }
        }

        // get route key/name
        $routeKey = array_keys($matches)[0];
        $len = strlen($routeKey);

        // fix remainging match key
        $res = [];
        foreach ($matches as $key => $val) {
            if ($key != $routeKey) {
                $res[substr($key, 0, -$len)] = $val;
            }
        }

        return [ $routeKey, $res ];
    }
}
