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
 * ParserInterface
 *
 * Route regular expression parser
 *
 * @interface
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
interface ParserInterface
{
    /**
     * Parse '/blog/{section}[/{year:\d+}[/{month:\d+}[/{date:\d+}]]]'
     *
     * @param  string $name regex name
     * @param  string $pattern pattern to parse
     * @return string
     * @access public
     * @internal
     */
    public function parse(
        /*# string */ $name,
        /*# string */ $pattern
    )/*# : string */;

    /**
     * Match an URL, return regex name and matched parameters
     *
     * @param  string $url
     * @return array|false [ $name, $matched ] or false
     * @access public
     * @internal
     */
    public function match(/*# string */ $url);
}
