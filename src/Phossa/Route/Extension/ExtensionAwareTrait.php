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

use Phossa\Route\Message\Message;
use Phossa\Route\Context\ResultInterface;
use Phossa\Route\Exception\LogicException;

/**
 * ExtensionAwareTrait
 *
 * @trait
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @see     ExtensionAwareInterface
 * @version 1.0.0
 * @since   1.0.0 added
 */
trait ExtensionAwareTrait
{
    /**
     * stages extension
     *
     * @var    array
     * @access protected
     */
    protected $stages   = [];

    /**
     * mark for sorted stage
     *
     * @var    array
     * @access protected
     */
    protected $sorted   = [];

    /**
     * counter
     *
     * @var    int
     * @access protected
     */
    protected $counter  = 0;

    /**
     * @inheritDoc
     */
    public function addExtension(
        callable $extension,
        /*# string */ $stage = '',
        /*# int */ $priority = 50
    ) {
        // extension object
        if (is_object($extension) && $extension instanceof ExtensionInterface) {
            $stages = $extension->stagesHandling();
            foreach ($stages as $stg => $pri) {
                $this->stages[$stg][$this->getPriority($pri)] = $extension;
                unset($this->sorted[$stg]);
            }

        // other type of callable
        } else {
            if ('' === $stage) {
                throw new LogicException(
                    Message::get(Message::STAGE_REQUIRED),
                    Message::STAGE_REQUIRED
                );
            }
            $this->stages[$stage][$this->getPriority($priority)] = $extension;
            unset($this->sorted[$stage]);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function hasExtension(/*# string */ $stage = '')/*# : bool */
    {
        return empty($stage) ?
            (bool) count($this->stages) :
            isset($this->stages[$stage]);
    }

    /**
     * @inheritDoc
     */
    public function runExtensions(
        /*# string */ $stage,
        ResultInterface $result
    )/*# : bool */ {
        if ($this->sortExtensions($stage)) {
            foreach ($this->sorted[$stage] as $ext) {
                if ($ext($stage, $result)) {
                    continue;
                }
                return false;
            }
        }
        return true;
    }

    /**
     * Sort extensions by priority for $stage
     *
     * @param  string $stage extension stage
     * @return bool
     * @access protected
     */
    protected function sortExtensions(/*# string */ $stage)/*# : bool */
    {
        if ($this->hasExtension($stage)) {
            if (!isset($this->sorted[$stage])) {
                ksort($this->stages[$stage]);
                $this->sorted[$stage] = &$this->stages[$stage];
            }
            return true;
        }
        return false;
    }

    /**
     * Get priority keys
     *
     * @param  int $priority original priority
     * @return int
     * @access protected
     */
    protected function getPriority(/*# int */ $priority)/*# : int */
    {
        return (int) ($priority % 100 * 100000 + ++$this->counter);
    }
}
