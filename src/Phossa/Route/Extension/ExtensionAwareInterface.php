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

namespace Phossa\Route\Extension;

use Phossa\Route\Context\ResultInterface;
use Phossa\Route\Exception\LogicException;

/**
 * ExtensionAwareInterface
 *
 * @interface
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @see     ExtensionInterface
 * @version 1.0.0
 * @since   1.0.0 added
 */
interface ExtensionAwareInterface
{
    /**
     * Add extensions
     *
     * @param  callable $extension
     * @param  string $stage stage to process
     * @param  int $priority  range 0 - 100
     * @return self
     * @throws LogicException if stage not found
     * @access public
     * @api
     */
    public function addExtension(
        callable $extension,
        /*# string */ $stage = '',
        /*# int */ $priority = 50
    );

    /**
     * Has extensions
     *
     * @param  string $stage stage to process
     * @return bool
     * @access public
     * @internal
     */
    public function hasExtension(/*# string */ $stage = '')/*# : bool */;

    /**
     * Execute extensions at different extension stage.
     *
     * @param  string $stage current stage
     * @param  ResultInterface $result matching result object
     * @return bool
     * @access public
     * @internal
     */
    public function runExtensions(
        /*# string */ $stage,
        ResultInterface $result
    )/*# : bool */;
}
