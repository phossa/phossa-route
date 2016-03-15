<?php
/**
 * Phossa Project
 *
 * PHP version 5.4
 *
 * @category  Package
 * @package   Phossa\Route
 * @author    Hong Zhang <phossa@126.com>
 * @copyright 2015 phossa.com
 * @license   http://mit-license.org/ MIT License
 * @link      http://www.phossa.com/
 */
/*# declare(strict_types=1); */

namespace Phossa\Route\Message;

use Phossa\Shared\Message\MessageAbstract;

/**
 * Message class for Phossa\Route
 *
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Phossa\Shared\Message\MessageAbstract
 * @version 1.0.0
 * @since   1.0.0 added
 */
class Message extends MessageAbstract
{
    /**#@+
     * @var   int
     */

    /**
     * Route "%s" duplicated for method "%s"
     */
    const ROUTE_DUPLICATED      = 1603111803;

    /**
     * No route should be added for "%s"
     */
    const ROUTE_DISALLOWED      = 1603111804;

    /**
     * Please provide stage name
     */
    const STAGE_REQUIRED        = 1603111805;

    /**
     * Pattern "%s" is malformed
     */
    const PATTERN_MALFORMED     = 1603111806;

    /**
     * Add collector "%s"
     */
    const DEBUG_ADD_COLLECTOR   = 1603111807;

    /**
     * Add extension "%s" at stage "%s"
     */
    const DEBUG_ADD_EXTENSION   = 1603111808;

    /**
     * "%s" run extension "%s" at stage "%s"
     */
    const DEBUG_RUN_EXTENSION   = 1603111809;

    /**
     * Set collector "%s" handler %s
     */
    const DEBUG_SET_C_HANDLER   = 1603111810;

    /**
     * Add route "%s" for methods %s
     */
    const DEBUG_ADD_ROUTE       = 1603111811;

    /**
     * Add parser "%s" to "%s"
     */
    const DEBUG_ADD_PARSER      = 1603111812;

    /**
     * Url "%s" matches route "%s" in "%s"
     */
    const DEBUG_MATCH_ROUTE     = 1603111813;

    /**
     * Parse pattern "%s" into "%s"
     */
    const DEBUG_PARSE_PATTERN   = 1603111814;

    /**
     * Matched with regex "%s"
     */
    const DEBUG_MATCH_REGEX     = 1603111815;

    /**
     * Add handler "%s" to "%s"
     */
    const DEBUG_ADD_HANDLER     = 1603111816;

    /**#@-*/

    /**
     * {@inheritdoc}
     */
    protected static $messages = [
        self::ROUTE_DUPLICATED      => 'Route "%s" duplicated for method "%s"',
        self::ROUTE_DISALLOWED      => 'No route should be added for "%s"',
        self::STAGE_REQUIRED        => 'Please provide stage name',
        self::PATTERN_MALFORMED     => 'Pattern "%s" is malformed',
        self::DEBUG_ADD_COLLECTOR   => 'Add collector "%s"',
        self::DEBUG_ADD_EXTENSION   => 'Add extension "%s" at stage "%s"',
        self::DEBUG_RUN_EXTENSION   => '"%s" run extension "%s" at stage "%s"',
        self::DEBUG_SET_C_HANDLER   => 'Set collector "%s" handler %s',
        self::DEBUG_ADD_ROUTE       => 'Add route "%s" for methods %s',
        self::DEBUG_ADD_PARSER      => 'Add parser "%s" to "%s"',
        self::DEBUG_MATCH_ROUTE     => 'Url "%s" matches route "%s" in "%s"',
        self::DEBUG_PARSE_PATTERN   => 'Parse pattern "%s" into "%s"',
        self::DEBUG_MATCH_REGEX     => 'Matched with regex "%s"',
        self::DEBUG_ADD_HANDLER     => 'Add handler "%s" to "%s"',
    ];
}
