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
 * ParserGcb
 *
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
class ParserGcb extends ParserAbstract
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
     * group position map
     *
     * @var    array
     * @access protected
     */
    protected $maps  = [];

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
     * another cache
     *
     * @var    string[]
     * @access protected
     */
    protected $xmap  = [];

    /**
     * @inheritDoc
     */
    public function parse(
        /*# string */ $name,
        /*# string */ $pattern
    )/*# : string */ {
        list($regex, $map)  = $this->convert($pattern);
        $this->regex[$name] = $regex;
        $this->maps[$name]  = $map;
        $this->modified = true;

        return $regex;
    }

    /**
     * @inheritDoc
     */
    public function match(/*# string */ $url)
    {
        $matches = [];
        foreach ($this->getRegexData() as $i => $regex) {
            if (preg_match($regex, $url, $matches)) {
                $map = array_flip($this->xmap[$i]);
                $key = $map[count($matches) - 1];
                return $this->fixMatches($key, $matches);
            }
        }
        return false;
    }

    /**
     * Convert to regex
     *
     * @param  string $pattern pattern to parse
     * @return array
     * @access protected
     */
    protected function convert(/*# string */ $pattern)/*# : array */
    {
        // regex
        $groupname   = "\s*([a-zA-Z][a-zA-Z0-9_]*)\s*";
        $grouptype   = ":\s*([^{}]*(?:\{(?-1)\}[^{}]*)*)";
        $placeholder = sprintf("\{%s(?:%s)?\}", $groupname, $grouptype);
        $segmenttype = "[^/]++";

        // count placeholders
        $map = $m = [];
        if (preg_match_all('~'. $placeholder .'~x', $pattern, $m)) {
            $map = $m[1];
        }

        // full regex
        $result = preg_replace([
                '~' . $placeholder . '(*SKIP)(*FAIL) | \[~x',
                '~' . $placeholder . '(*SKIP)(*FAIL) | \]~x',
                '~\{' . $groupname . '\}~x',
                '~' . $placeholder . '~x',
            ], [
                '(?:',  // replace '['
                ')?',   // replace ']'
                '{\\1:' . $segmenttype . '}',   // add segementtype
                '(\\2)'                         // group it
            ], strtr('/' . trim($pattern, '/'), $this->shortcuts)
        );
        return [ $result, $map ];
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
        $this->data = array_chunk($this->regex, $this->chunk, true);

        // count groups
        foreach ($this->data as $i => $arr) {
            $map = $this->getMapData($arr, $this->maps);
            $str = '~^(?|';
            foreach ($arr as $k => $reg) {
                $str .= $reg .
                    str_repeat('()', $map[$k] - count($this->maps[$k])) . '|';
            }
            $this->data[$i] = substr($str, 0, -1) . ')$~x';
            $this->xmap[$i] = $map;
        }

        // save to cache here
        $this->modified = false;

        return $this->data;
    }

    /**
     *
     * @param  array $arr
     * @param  array $maps
     * @return array
     * @access protected
     */
    protected function getMapData(array $arr, array $maps)/*#: array */
    {
        $new1 = [];
        $keys = array_keys($arr);
        foreach ($keys as $k) {
            $new1[$k] = count($maps[$k]) + 1; // # of PH for route $k
        }
        $new2 = array_flip($new1);
        $new3 = array_flip($new2);

        foreach ($keys as $k) {
            if (!isset($new3[$k])) {
                foreach (range(1, 200) as $i) {
                    $cnt = $new1[$k] + $i;
                    if (!isset($new2[$cnt])) {
                        $new2[$cnt] = $k;
                        $new3[$k] = $cnt;
                        break;
                    }
                }
            }
        }
        return $new3;
    }

    /**
     * Fix matched placeholders, return with unique route key
     *
     * @param  string $name the route key/name
     * @param  array $matches desc
     * @return array [ $name, $matches ]
     * @access protected
     */
    protected function fixMatches(
        /*# string */ $name,
        array $matches
    )/*# : array */ {
        $res = [];
        $map = $this->maps[$name];
        foreach ($matches as $idx => $val) {
            if ($idx > 0 && '' !== $val) {
                $res[$map[$idx - 1]] = $val;
            }
        }
        return [ $name, $res ];
    }
}
