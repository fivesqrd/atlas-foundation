<?php
require_once('TestAsset/MockModelBarebones/User.php');
require_once('TestAsset/MockModelBarebones/User/Mapper.php');
require_once('TestAsset/MockModelBarebones/User/Query.php');
require_once('TestAsset/MockModelBarebones/User/Named.php');

use PHPUnit\Framework\TestCase;
use Atlas\Factory;
use MockModelBarebones as Model;


/**
 * @covers \Atlas\Factory
 */
class FactoryTest extends TestCase
{
    protected $_config = array(
        'read' => array(
            'driver'   => 'Pdo_Mysql',
            'dbname'   => 'tact',
            'username' => 'username',
            'password' => 'password',
            'host'     => '192.168.254.10'
        ),
        'write' => array(
            'driver'   => 'Pdo_Mysql',
            'dbname'   => 'tact',
            'username' => 'username',
            'password' => 'password',
            'host'     => '192.168.254.10'
        ),
    );
    
    public function testFactoryIsValidatingConfig()
    {
        $this->setExpectedException('Atlas\Exception', 'Malformed write db config provided');
        new Factory(array(), FactoryTest::class);
    }

    public function testFactoryIsInstantiatingWithValidConfig()
    {
        $factory = new Factory($this->_config, Model\User::class);

        $this->assertInstanceOf(
            Factory::class,
            $factory 
        );
    }

    public function testQueryMethodIsReturningValidObject()
    {
        $factory = new Factory($this->_config, Model\User::class);

        $this->assertInstanceOf(
            Model\User\Query::class,
            $factory->query() 
        );
    }

    public function testNamedMethodIsReturningValidObject()
    {
        $factory = new Factory($this->_config, Model\User::class);

        $this->assertInstanceOf(
            Model\User\Named::class,
            $factory->named() 
        );
    }
}
