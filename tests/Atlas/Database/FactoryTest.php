<?php
require_once('TestAsset/MockModelBarebones/User/Mapper.php');

use PHPUnit\Framework\TestCase;

/**
 * @covers \Atlas\Factory
 */
class FactoryTest extends TestCase
{
    protected $_config = array(
        'read' => array(
            'dsn'      => 'sqlite::memory:',
            'username' => 'username',
            'password' => 'password',
        ),
        'write' => array(
            'dsn'      => 'sqlite::memory:',
            'username' => 'username',
            'password' => 'password',
        ),
    );
    
    public function testFactoryIsValidatingConfig()
    {
        $this->setExpectedException(
            'Atlas\Database\Exception', 
            'Malformed db adapter write config specified'
        );

        $factory = new Atlas\Database\Factory(
            array(), new Atlas\Database\Resolver('User') 
        );

        $factory->adapter('write');
    }

    public function testWriteMethodIsReturningValidObject()
    {
        $resolver = new Atlas\Database\Resolver('MockModelBarebones\User');

        $factory = new Atlas\Database\Factory(
            $this->_config, $resolver 
        );

        $this->assertInstanceOf(
            Atlas\Database\Write::class,
            $factory->write($resolver) 
        );
    }

    public function testAdapterMethodIsReturningPdoObject()
    {
        $resolver = new Atlas\Database\Resolver('MockModelBarebones\User');

        $factory = new Atlas\Database\Factory(
            $this->_config, $resolver 
        );

        $this->assertInstanceOf(
            'PDO',
            $factory->adapter('read') 
        );
    }

    public function testGetAliasMethodExists()
    {
        $stub = $this->getMockBuilder('User\Mapper')
            ->setMethods(array('getAlias'))
            ->getMock();

        $stub->method('getAlias')->willReturn('u');

        $stub->expects($this->once())
            ->method('getAlias');

        $stub->getAlias();
    }
}
