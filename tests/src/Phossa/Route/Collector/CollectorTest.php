<?php
namespace Phossa\Route\Collector;

use Phossa\Route\Route;
use Phossa\Route\Status;
use Phossa\Route\Regex\ParserStd;
use Phossa\Route\Regex\ParserGcb;
use Phossa\Route\Context\Result;
use Phossa\Route\Context\Request;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-03-11 at 11:42:56.
 */
class CollectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Collector
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Collector(new ParserStd, [ 'chunk' => 3 ]);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * Call protected/private method of a class.
     *
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    protected function invokeMethod($methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass($this->object);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($this->object, $parameters);
    }

    /**
     * getPrivateProperty
     *
     * @param 	string $propertyName
     * @return	the property
     */
    public function getPrivateProperty($propertyName)
    {
        $reflector = new \ReflectionClass($this->object);
        $property = $reflector->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($this->object);
    }

    /**
     * @covers Phossa\Route\Collector\Collector::addRoute
     * @expectedException Phossa\Route\Exception\LogicException
     * @expectedExceptionCode Phossa\Route\Message\Message::ROUTE_DUPLICATED
     */
    public function testAddRoute1()
    {
        $this->object->addRoute(new Route('GET|POST', '/'));
        $this->object->addRoute(new Route('POST', '/'));
    }

    /**
     * @covers Phossa\Route\Collector\Collector::addRoute
     */
    public function testAddRoute2()
    {
        $this->object->addRoute(new Route('GET|HEAD', '/'));
        $this->object->addRoute(new Route('POST', '/user'));
        $this->assertTrue(2 == count($this->getPrivateProperty('routes')));
    }

    /**
     * @covers Phossa\Route\Collector\Collector::addRoute
     * @expectedException Phossa\Route\Exception\LogicException
     * @expectedExceptionCode Phossa\Route\Message\Message::ROUTE_DUPLICATED
     */
    public function testAddRoute3()
    {
        $this->object = new Collector(new ParserGcb, [ 'chunk' => 3 ]);
        $this->object->addRoute(new Route('GET|POST', '/'));
        $this->object->addRoute(new Route('POST', '/'));
    }

    /**
     * @covers Phossa\Route\Collector\Collector::addRoute
     */
    public function testAddRoute4()
    {
        $this->object = new Collector(new ParserGcb, [ 'chunk' => 3 ]);
        $this->object->addRoute(new Route('GET|HEAD', '/'));
        $this->object->addRoute(new Route('POST', '/user'));
        $this->assertTrue(2 == count($this->getPrivateProperty('routes')));
    }

    /**
     * test parameter capture
     *
     * @covers Phossa\Route\Collector\Collector::match
     */
    public function testMatch1()
    {
        $this->object->addRoute(new Route('GET,POST', '/user[/{name:c}]'));
        $result = new Result(new Request('GET', '/user/phossa'));
        if ($this->invokeMethod('match', [$result])) {
            $this->assertEquals(Status::OK, $result->getStatus());
            $this->assertEquals(['name' => 'phossa'], $result->getParameter());
        } else {
            throw new \Exception('bad');
        }
    }

    /**
     * test parameter capture, test GCB
     *
     * @covers Phossa\Route\Collector\Collector::match
     */
    public function testMatch11()
    {
        $this->object = new Collector(new ParserGcb, [ 'chunk' => 3 ]);
        $this->object->addRoute(new Route('GET,POST', '/user[/{name:c}]'));
        $result = new Result(new Request('GET', '/user/phossa'));
        if ($this->invokeMethod('match', [$result])) {
            $this->assertEquals(Status::OK, $result->getStatus());
            $this->assertEquals(['name' => 'phossa'], $result->getParameter());
        } else {
            throw new \Exception('bad');
        }
    }

    /**
     * test optional segment
     *
     * @covers Phossa\Route\Collector\Collector::match
     */
    public function testMatch2()
    {
        $this->object->addRoute(new Route(
            'GET,POST',
            '/blog[/{section:xd}][/{year:d}[/{month:d}[/{date:d}]]]',
            null,
            ['section' => 'list']
        ));


        $res1 = new Result(new Request('GET', '/blog/'));
        if ($this->invokeMethod('match', [$res1])) {
            $this->assertEquals(Status::OK, $res1->getStatus());
            $this->assertEquals(['section' => 'list'], $res1->getParameter());
        } else {
            throw new \Exception('bad');
        }

        $res2 = new Result(new Request('GET', '/blog/edit/'));
        if ($this->invokeMethod('match', [$res2])) {
            $this->assertEquals(Status::OK, $res2->getStatus());
            $this->assertEquals(['section' => 'edit'], $res2->getParameter());
        } else {
            throw new \Exception('bad');
        }

        $res3 = new Result(new Request('GET', '/blog/edit/2016'));
        if ($this->invokeMethod('match', [$res3])) {
            $this->assertEquals(Status::OK, $res3->getStatus());
            $this->assertEquals(
                ['section' => 'edit', 'year' => '2016'],
                $res3->getParameter()
            );
        } else {
            throw new \Exception('bad');
        }

        $res4 = new Result(new Request('GET', '/blog/2016'));
        if ($this->invokeMethod('match', [$res4])) {
            $this->assertEquals(Status::OK, $res4->getStatus());
            $this->assertEquals(
                ['section' => 'list', 'year' => '2016'],
                $res4->getParameter()
            );
        } else {
            throw new \Exception('bad');
        }

        $res5 = new Result(new Request('GET', '/blog/add/2016/04'));
        if ($this->invokeMethod('match', [$res5])) {
            $this->assertEquals(Status::OK, $res5->getStatus());
            $this->assertEquals(
                ['section' => 'add', 'year' => '2016', 'month' => '04'],
                $res5->getParameter()
            );
        } else {
            throw new \Exception('bad');
        }
    }

    /**
     * test optional segment, test GCB
     *
     * @covers Phossa\Route\Collector\Collector::match
     */
    public function testMatch21()
    {
        $this->object = new Collector(new ParserGcb, [ 'chunk' => 3 ]);
        $this->object->addRoute(new Route(
            'GET,POST',
            '/blog[/{section:xd}][/{year:d}[/{month:d}[/{date:d}]]]',
            null,
            ['section' => 'list']
        ));


        $res1 = new Result(new Request('GET', '/blog/'));
        if ($this->invokeMethod('match', [$res1])) {
            $this->assertEquals(Status::OK, $res1->getStatus());
            $this->assertEquals(['section' => 'list'], $res1->getParameter());
        } else {
            throw new \Exception('bad');
        }

        $res2 = new Result(new Request('GET', '/blog/edit/'));
        if ($this->invokeMethod('match', [$res2])) {
            $this->assertEquals(Status::OK, $res2->getStatus());
            $this->assertEquals(['section' => 'edit'], $res2->getParameter());
        } else {
            throw new \Exception('bad');
        }

        $res3 = new Result(new Request('GET', '/blog/edit/2016'));
        if ($this->invokeMethod('match', [$res3])) {
            $this->assertEquals(Status::OK, $res3->getStatus());
            $this->assertEquals(
                ['section' => 'edit', 'year' => '2016'],
                $res3->getParameter()
            );
        } else {
            throw new \Exception('bad');
        }

        $res4 = new Result(new Request('GET', '/blog/2016'));
        if ($this->invokeMethod('match', [$res4])) {
            $this->assertEquals(Status::OK, $res4->getStatus());
            $this->assertEquals(
                ['section' => 'list', 'year' => '2016'],
                $res4->getParameter()
            );
        } else {
            throw new \Exception('bad');
        }

        $res5 = new Result(new Request('GET', '/blog/add/2016/04'));
        if ($this->invokeMethod('match', [$res5])) {
            $this->assertEquals(Status::OK, $res5->getStatus());
            $this->assertEquals(
                ['section' => 'add', 'year' => '2016', 'month' => '04'],
                $res5->getParameter()
            );
        } else {
            throw new \Exception('bad');
        }
    }

    /**
     * test regex combination
     *
     * @covers Phossa\Route\Collector\Collector::match
     */
    public function testMatch3()
    {
        $this->object->addRoute(new Route(
            'GET,POST',
            '/blog1[/{section:xd}][/{year:d}[/{month:d}[/{date:d}]]]',
            null,
            ['section' => 'list']
        ));
        $this->object->addRoute(new Route(
            'GET,POST',
            '/blog2[/{section:xd}][/{year:d}[/{month:d}[/{date:d}]]]',
            null,
            ['section' => 'list']
        ));
        $this->object->addRoute(new Route(
            'GET,POST',
            '/blog3[/{section:xd}][/{year:d}[/{month:d}[/{date:d}]]]',
            null,
            ['section' => 'list']
        ));
        $this->object->addRoute(new Route(
            'GET,POST',
            '/blog4[/{section:xd}][/{year:d}[/{month:d}[/{date:d}]]]',
            null,
            ['section' => 'list']
        ));
        $this->object->addRoute(new Route(
            'GET,POST',
            '/blog5[/{section:xd}][/{year:d}[/{month:d}[/{date:d}]]]',
            null,
            ['section' => 'list']
        ));
        $this->object->addRoute(new Route(
            'GET,POST',
            '/blog6[/{section:xd}][/{year:d}[/{month:d}[/{date:d}]]]',
            null,
            ['section' => 'list']
        ));
        $this->object->addRoute(new Route(
            'GET,POST',
            '/blog7[/{section:xd}][/{year:d}[/{month:d}[/{date:d}]]]',
            null,
            ['section' => 'list']
        ));

        $res1 = new Result(new Request('GET', '/blog7/'));
        if ($this->invokeMethod('match', [$res1])) {
            $this->assertEquals(Status::OK, $res1->getStatus());
            $this->assertEquals(['section' => 'list'], $res1->getParameter());
        } else {
            throw new \Exception('bad');
        }
    }

    /**
     * test regex combination, test GCB
     *
     * @covers Phossa\Route\Collector\Collector::match
     */
    public function testMatch31()
    {
        $this->object = new Collector(new ParserGcb, [ 'chunk' => 3 ]);
        $this->object->addRoute(new Route(
            'GET,POST',
            '/blog1[/{section:xd}][/{year:d}[/{month:d}[/{date:d}]]]',
            null,
            ['section' => 'list']
        ));
        $this->object->addRoute(new Route(
            'GET,POST',
            '/blog2[/{section:xd}][/{year:d}[/{month:d}[/{date:d}]]]',
            null,
            ['section' => 'list']
        ));
        $this->object->addRoute(new Route(
            'GET,POST',
            '/blog3[/{section:xd}][/{year:d}[/{month:d}[/{date:d}]]]',
            null,
            ['section' => 'list']
        ));
        $this->object->addRoute(new Route(
            'GET,POST',
            '/blog4[/{section:xd}][/{year:d}[/{month:d}[/{date:d}]]]',
            null,
            ['section' => 'list']
        ));
        $this->object->addRoute(new Route(
            'GET,POST',
            '/blog5[/{section:xd}][/{year:d}[/{month:d}[/{date:d}]]]',
            null,
            ['section' => 'list']
        ));
        $this->object->addRoute(new Route(
            'GET,POST',
            '/blog6[/{section:xd}][/{year:d}[/{month:d}[/{date:d}]]]',
            null,
            ['section' => 'list']
        ));
        $this->object->addRoute(new Route(
            'GET,POST',
            '/blog7[/{section:xd}][/{year:d}[/{month:d}[/{date:d}]]]',
            null,
            ['section' => 'list']
        ));

        $res1 = new Result(new Request('GET', '/blog7/'));
        if ($this->invokeMethod('match', [$res1])) {
            $this->assertEquals(Status::OK, $res1->getStatus());
            $this->assertEquals(['section' => 'list'], $res1->getParameter());
        } else {
            throw new \Exception('bad');
        }
    }

    /**
     * test method not allowed
     *
     * @covers Phossa\Route\Collector\Collector::match
     */
    public function testMatch4()
    {
        $this->object->addRoute(new Route('GET,POST', '/user[/{name:c}]'));
        $result = new Result(new Request('HEAD', '/user/phossa'));
        if (!$this->invokeMethod('match', [$result])) {
            $this->assertEquals(Status::METHOD_NOT_ALLOWED, $result->getStatus());
        } else {
            throw new \Exception('bad');
        }
    }

    /**
     * test method not allowed, test GCB
     *
     * @covers Phossa\Route\Collector\Collector::match
     */
    public function testMatch41()
    {
        $this->object = new Collector(new ParserGcb, [ 'chunk' => 3 ]);
        $this->object->addRoute(new Route('GET,POST', '/user[/{name:c}]'));
        $result = new Result(new Request('HEAD', '/user/phossa'));
        if (!$this->invokeMethod('match', [$result])) {
            $this->assertEquals(Status::METHOD_NOT_ALLOWED, $result->getStatus());
        } else {
            throw new \Exception('bad');
        }
    }

    /**
     * test other filter, filter by server_name
     *
     * @covers Phossa\Route\Collector\Collector::match
     */
    public function testMatch5()
    {
        unset($_SERVER['SERVER_NAME']);
        $route = new Route('GET,POST', '/user[/{name:c}]');
        $route->addFilter('m.phossa.com', 'server_name');
        $this->object->addRoute($route);

        // failed
        $res1 = new Result(new Request('GET', '/user/phossa'));
        if (!$this->invokeMethod('match', [$res1])) {
            $this->assertEquals(Status::PRECONDITION_FAILED,
                $res1->getStatus());
        } else {
            throw new \Exception('bad');
        }

        // good
        $_SERVER['SERVER_NAME'] = 'm.phossa.com';
        $res2 = new Result(new Request('GET', '/user/phossa'));
        if ($this->invokeMethod('match', [$res2])) {
            $this->assertEquals(Status::OK, $res2->getStatus());
        } else {
            throw new \Exception('bad');
        }
    }


    /**
     * test other filter, filter by server_name, test GCB
     *
     * @covers Phossa\Route\Collector\Collector::match
     */
    public function testMatch51()
    {
        $this->object = new Collector(new ParserGcb, [ 'chunk' => 3 ]);

        unset($_SERVER['SERVER_NAME']);
        $route = new Route('GET,POST', '/user[/{name:c}]');
        $route->addFilter('m.phossa.com', 'server_name');
        $this->object->addRoute($route);

        // failed
        $res1 = new Result(new Request('GET', '/user/phossa'));
        if (!$this->invokeMethod('match', [$res1])) {
            $this->assertEquals(Status::PRECONDITION_FAILED,
                $res1->getStatus());
        } else {
            throw new \Exception('bad');
        }

        // good
        $_SERVER['SERVER_NAME'] = 'm.phossa.com';
        $res2 = new Result(new Request('GET', '/user/phossa'));
        if ($this->invokeMethod('match', [$res2])) {
            $this->assertEquals(Status::OK, $res2->getStatus());
        } else {
            throw new \Exception('bad');
        }
    }

    /**
     * test not match
     *
     * @covers Phossa\Route\Collector\Collector::match
     */
    public function testMatch6()
    {
        $this->object->addRoute(new Route('GET,POST', '/user[/{name:c}]'));
        $result = new Result(new Request('GET', '/user1/phossa'));
        if (!$this->invokeMethod('match', [$result])) {
            $this->assertEquals(Status::NOT_FOUND, $result->getStatus());
        } else {
            throw new \Exception('bad');
        }
    }

    /**
     * test not match, test GCB
     *
     * @covers Phossa\Route\Collector\Collector::match
     */
    public function testMatch61()
    {
        $this->object = new Collector(new ParserGcb, [ 'chunk' => 3 ]);
        $this->object->addRoute(new Route('GET,POST', '/user[/{name:c}]'));
        $result = new Result(new Request('GET', '/user1/phossa'));
        if (!$this->invokeMethod('match', [$result])) {
            $this->assertEquals(Status::NOT_FOUND, $result->getStatus());
        } else {
            throw new \Exception('bad');
        }
    }
}
