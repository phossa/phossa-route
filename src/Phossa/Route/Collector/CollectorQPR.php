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

namespace Phossa\Route\Collector;

use Phossa\Route\Status;
use Phossa\Route\RouteInterface;
use Phossa\Route\Message\Message;
use Phossa\Route\Context\ResultInterface;
use Phossa\Route\Exception\LogicException;

/**
 * CollectorQPR
 *
 * Query Parameter Routing (QPR)
 *
 * ```
 * http://servername/path/?r=controller-action-id-1-name-nick
 * ```
 *
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
class CollectorQPR extends CollectorAbstract
{
    use \Phossa\Shared\Pattern\SetPropertiesTrait;

    /**
     * variable name
     *
     * @var    string
     * @access protected
     */
    protected $varname = 'r';

    /**
     * seperator
     *
     * @var    string
     * @access protected
     */
    protected $seperator = '-';

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

    /**
     * @inheritDoc
     */
    public function addRoute(RouteInterface $route)
    {
        if ($route) {
            throw new LogicException(
                Message::get(Message::ROUTE_DISALLOWED, __CLASS__),
                Message::ROUTE_DISALLOWED
            );
        }
    }

    /**
     * Query Parameter Routing
     *
     * http://servername/path/?r=controller-action-id-1-name-nick
     *
     * {@inheritDoc}
     */
    protected function match(ResultInterface $result)/*# : bool */
    {
        $params = $result->getParameter();
        if (isset($params[$this->varname])) {
            // set status
            $result->setStatus(Status::OK);

            // parameters
            $parts = explode($this->seperator, $params[$this->varname]);
            $controller = array_shift($parts);
            $action = array_shift($parts);
            if (count($parts) % 2) {
                $result->setStatus(Status::BAD_REQUEST);
                return false;
            }

            $newparam = [];
            foreach ($parts as $i => $val) {
                if (0 === $i % 2) {
                    $newparam[$val] = $parts[$i + 1];
                }
            }
            $result->setParameter($newparam);
            $result->setHandler([$controller, $action]);
            return true;
        }

        $result->setStatus(Status::BAD_REQUEST);
        return false;
    }
}
