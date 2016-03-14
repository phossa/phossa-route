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
 * CollectorPPR
 *
 * Parameter Pairs Routing (PPR)
 *
 * Using parameter and value pairs like the following
 *
 * ```
 * http://servername/path/index.php/controller/action/id/1/name/nick
 * ```
 *
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
class CollectorPPR extends CollectorAbstract
{
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
     * Parameter Pairs Routing (PPR)
     *
     * /path/index.php/controller/action/id/1/name/nick
     *
     * {@inheritDoc}
     */
    protected function match(ResultInterface $result)/*# : bool */
    {
        $path = trim($result->getRequest()->getServerInfo('path_info'), ' /');
        if (count($parts = explode('/', $path)) > 1) {
            // set status
            $result->setStatus(Status::OK);

            // parameters
            $controller = array_shift($parts);
            $action = array_shift($parts);
            if (count($parts) % 2) {
                $result->setStatus(Status::BAD_REQUEST);
                return false;
            }

            $params = [];
            foreach ($parts as $i => $val) {
                if ($i % 2) {
                    // skip this
                } else {
                    $params[$val] = $parts[$i + 1];
                }
            }
            $result->setParameter($params);
            $result->setHandler([$controller, $action]);

            return true;
        }
        $result->setStatus(Status::BAD_REQUEST);
        return false;
    }
}
