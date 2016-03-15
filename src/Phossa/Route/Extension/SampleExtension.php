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

use Phossa\Route\Route;
use Phossa\Route\Dispatcher;
use Phossa\Route\Context\ResultInterface;
use Phossa\Route\Collector\CollectorAbstract;

/**
 * SampleExtension
 *
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
class SampleExtension implements ExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function stagesHandling()/*# : array */
    {
        return [
            Dispatcher::BEFORE_MATCH        => 50,
            Dispatcher::AFTER_MATCH         => 50,
            Dispatcher::BEFORE_DISPATCH     => 50,
            Dispatcher::AFTER_DISPATCH      => 50,
            CollectorAbstract::BEFORE_COLL  => 50,
            CollectorAbstract::AFTER_COLL   => 50,
            Route::BEFORE_ROUTE             => 50,
            Route::AFTER_ROUTE              => 50
        ];
    }

    /**
     * @inheritDoc
     */
    public function __invoke(
        ResultInterface $result,
        /*# string */ $stage = ''
    )/*# : bool */ {
        echo sprintf(
            "%s(%d) ",
            $stage,
            $result->getStatus()
        );
        return true;
    }
}
