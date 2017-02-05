<?php
require_once('Database/TestAsset/MockModelBarebones/User/Mapper.php');
require_once('Database/TestAsset/MockModelBarebones/User/Query.php');

use PHPUnit\Framework\TestCase;

/**
 * @covers \Atlas\Factory
 */
class ProxyTest extends TestCase
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

    public function testQueryIsReturningValidObject()
    {
        $resolver = new Atlas\Database\Resolver('MockModelBarebones\User');

        $factory = new Atlas\Database\Factory($this->_config);

        $proxy = new Atlas\Proxy($factory, $resolver);

        $this->assertInstanceOf(
            'MockModelBarebones\User\Query',
            $proxy->query() 
        );
    }
}
