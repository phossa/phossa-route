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

    /**#@-*/

    /**
     * {@inheritdoc}
     */
    protected static $messages = [
        self::ROUTE_DUPLICATED  => 'Route "%s" duplicated for method "%s"',
        self::ROUTE_DISALLOWED  => 'No route should be added for "%s"',
        self::STAGE_REQUIRED    => 'Please provide stage name',
        self::PATTERN_MALFORMED => 'Pattern "%s" is malformed',
    ];
}
