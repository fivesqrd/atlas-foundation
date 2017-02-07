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
