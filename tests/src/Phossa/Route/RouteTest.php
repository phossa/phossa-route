<?php
namespace Phossa\Route;

use Phossa\Route\Context\Result as Status;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2016-03-11 at 11:42:57.
 */
class RouteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Route
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Route;
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
     * @covers Phossa\Route\Route::setHandler
     */
    public function testSetHandler()
    {
        $this->object->setHandler('test', Status::METHOD_NOT_ALLOWED);
        $this->assertTrue('test' ===
            $this->object->getHandler(Status::METHOD_NOT_ALLOWED));
    }

    /**
     * @covers Phossa\Route\Route::getHandler
     */
    public function testGetHandler()
    {
        $this->assertTrue(is_null($this->object->getHandler(Status::OK)));
    }

    /**
     * @covers Phossa\Route\Route::setPattern
     */
    public function testSetPattern()
    {
        $pattern = '/user/{action:c}[/{id:d}]';
        $this->object->setPattern($pattern);
        $this->assertEquals($pattern, $this->object->getPattern());
    }

    /**
     * @covers Phossa\Route\Route::getPattern
     */
    public function testGetPattern()
    {
        $this->assertEquals('', $this->object->getPattern());
    }

    /**
     * @covers Phossa\Route\Route::setMethods
     */
    public function testSetMethods()
    {
        $this->object->setMethods('GET| ,POST ');
        $this->assertEquals(['GET','POST'], $this->object->getMethods());
    }

    /**
     * @covers Phossa\Route\Route::getMethods
     */
    public function testGetMethods()
    {
        $this->assertEquals(['GET'], $this->object->getMethods());
    }

    /**
     * @covers Phossa\Route\Route::addFilter
     */
    public function testAddFilter()
    {
        $this->object->addFilter('m.phossa.com', 'server_name');
        $this->assertArrayHasKey('server_name', $this->object->getFilters());
    }

    /**
     * @covers Phossa\Route\Route::getFilters
     */
    public function testGetFilters()
    {
        $this->assertTrue(empty($this->object->getFilters()));
        $this->object->addFilter('m.phossa.com', 'server_name');
        $this->assertEquals(['server_name' => 'm.phossa.com'],
            $this->object->getFilters());
    }

    /**
     * @covers Phossa\Route\Route::setDefault
     */
    public function testSetDefault()
    {
        $this->object->setDefault(['id' => 'phossa']);
        $this->assertEquals(['id' => 'phossa'], $this->object->getDefault());

        $this->object->setDefault(['id' => 'test']);
        $this->assertEquals(['id' => 'test'], $this->object->getDefault());


    }

    /**
     * @covers Phossa\Route\Route::getDefault
     */
    public function testGetDefault()
    {
        $this->assertEquals([], $this->object->getDefault());
    }
}
