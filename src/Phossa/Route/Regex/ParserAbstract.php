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
 * ParserAbstract
 *
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
abstract class ParserAbstract implements ParserInterface
{
    use \Phossa\Shared\Pattern\SetPropertiesTrait;

    /**
     * shortcuts
     *
     * @var    string[]
     * @access protected
     */
    protected $shortcuts = [
        ':d}'   => ':[0-9]++}',             // digit only
        ':l}'   => ':[a-z]++}',             // lower case
        ':u}'   => ':[A-Z]++}',             // upper case
        ':a}'   => ':[0-9a-zA-Z]++}',       // alphanumeric
        ':c}'   => ':[0-9a-zA-Z+_\-\.]++}', // common chars
        ':nd}'  => ':[^0-9/]++}',           // not digits
        ':xd}'  => ':[^0-9/][^/]*+}',       // no leading digits
    ];

    /**
     * Constructor
     *
     * @param  array $settings
     * @access public
     * @api
     */
    public function __construct(array $settings = [])
    {
        $this->setProperties($settings);
    }
}
