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

use Phossa\Route\Status;
use Phossa\Route\Dispatcher;
use Phossa\Route\Context\ResultInterface;

/**
 * RedirectToHttpsExtension
 *
 * Redirect any HTTP request to HTTPS
 *
 * @package Phossa\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 1.0.0
 * @since   1.0.0 added
 */
class RedirectToHttpsExtension implements ExtensionInterface
{
    /**
     * Fixed direct url
     *
     * @var    string
     * @access protected
     */
    protected $url;

    /**
     * constructor
     *
     * @param  string $redirectUrl redirection URL if any
     * @access public
     * @api
     */
    public function __construct($redirectUrl = '')
    {
        if ($redirectUrl) {
            $this->url = $redirectUrl;
        }
    }

    /**
     * Before match anything check scheme first
     *
     * {@inheritDoc}
     */
    public function stagesHandling()/*# : array */
    {
        return [ Dispatcher::BEFORE_MATCH => 10 ];
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke(
        ResultInterface $result,
        /*# string */ $stage = ''
    )/*# : bool */ {
        if (!$result->getRequest()->getServerInfo('https') && $stage) {
            $result->setStatus(Status::MOVED_PERMANENTLY)
                   ->setParameter(
                        '__REDIRECT_URL__',
                        $this->url ?: $this->getUrl(
                           (array) $result->getRequest()->getServerInfo()
                        ))
                   ->setHandler(function (ResultInterface $result) {
                        header(
                            "Location: ".
                            $result->getParameter('__REDIRECT_URL__'),
                            true,
                            Status::MOVED_PERMANENTLY
                        );
                        die();
                   });
            // skip rest of the match/dispatch procedure
            return false;
        }
        return true;
    }

    /**
     * Construct URL with HTTPS scheme
     *
     * @param  array $server server info
     * @return string
     * @access protected
     */
    protected function getUrl(array $server)/*# : string */
    {
        $host = isset($server['HTTP_HOST']) ?
            $server['HTTP_HOST'] :
            $server['SERVER_NAME'];
        return 'https://' . $host . $server['REQUEST_URI'];
    }
}
