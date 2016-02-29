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
     * Wrong log level name
     */
    const WRONG_LOG_LEVEL       = 1510301240;

    /**#@-*/

    /**
     * {@inheritdoc}
     */
    protected static $messages = [
    ];
}
