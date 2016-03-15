<?php
namespace Phossa\Route\Context;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-03-11 at 11:42:58.
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Request
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        // fake data
        $_COOKIE = ['cookie' => 'test'];
        $_POST   = ['post' => 'test'];

        $_SERVER = [];
        $this->object = new Request('POST', '/user/list/1234?id=phossa');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $_COOKIE = [];
        $_POST = [];
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
     * test protected fixUrlPath()
     *
     * @covers Phossa\Route\Context\Request::fixUrlPath
     */
    public function testFixUrlPath()
    {
        // fake path


    }

    /**
     * @covers Phossa\Route\Context\Request::getHttpMethod
     */
    public function testGetHttpMethod()
    {
        $this->assertEquals('POST', $this->object->getHttpMethod());
    }

    /**
     * @covers Phossa\Route\Context\Request::getPathInfo
     */
    public function testGetPathInfo()
    {
        $this->assertEquals('/user/list/1234', $this->object->getPathInfo());

        // fake stuff
        $_SERVER['REQUEST_URI'] = '/user/add/12?ddsfd=adfad';
        $_SERVER['PATH_INFO'] = '/add/12';
        $this->object = new Request('POST', '/user/list/1234?id=phossa');

        $this->assertEquals('/list/1234', $this->object->getPathInfo());
    }

    /**
     * @covers Phossa\Route\Context\Request::getServerInfo
     */
    public function testGetServerInfo()
    {
        $server = $this->object->getServerInfo();
        $this->assertArrayHasKey('REQUEST_URI', $server);

        $this->assertEquals(
            '/user/list/1234?id=phossa',
            $this->object->getServerInfo('request_uri')
        );
    }

    /**
     * @covers Phossa\Route\Context\Request::getRequestInfo
     */
    public function testGetRequestInfo()
    {
        $this->assertArrayHasKey('id', $this->object->getRequestInfo());
        $this->assertArrayHasKey('post', $this->object->getRequestInfo());
        $this->assertArrayHasKey('cookie', $this->object->getRequestInfo());
    }
}
