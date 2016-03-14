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

/**
 * ExtensionInterface
 *
 * @interface
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
interface ExtensionInterface
{
    /**
     * Routing stages with with priority (int, small number higher priority)
     *
     * @return array [ 'stage' => 50, ... ]
     * @access public
     * @api
     */
    public function stagesHandling()/*# : array */;

    /**
     * Make extension callable. MUST RETURN boolean !!
     *
     * If `false` returned, caller will skip the rest procedure
     *
     * @param  string $stage current stage
     * @param  ResultInterface $result result object
     * @return bool
     * @access public
     * @api
     */
    public function __invoke(
        /*# string */ $stage,
        ResultInterface $result
    )/*# : bool */;
}
