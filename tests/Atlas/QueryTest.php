<?php
require_once('TestAsset/MockModelBarebones/User.php');
require_once('TestAsset/MockModelBarebones/User/Mapper.php');
require_once('TestAsset/MockModelBarebones/User/Query.php');
require_once('TestAsset/MockModelBarebones/User/Named.php');

require_once('TestAsset/MockModelWithQueryMethods/User/Query.php');
require_once('TestAsset/MockModelWithQueryMethods/User/Mapper.php');

use PHPUnit\Framework\TestCase;
use Atlas\Factory;
use Atlas\Query;

/**
 * @covers \Atlas\Factory
 */
class QueryTest extends TestCase
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

    public function testFetchMethodIsReturningValidObject()
    {
        $factory = new Factory(
            $this->_config, MockModelBarebones\User::class
        );

        $this->assertInstanceOf(
            Query\Fetch::class, $factory->query()->fetch()
        );
    }

    public function testSqlIsEmptyAtFirst()
    {
        $factory = new Factory(
            $this->_config,  MockModelBarebones\User::class
        );

        $this->assertNull(
            $factory->query()->getSql() 
        );
    }

    /*
    public function testSqlIsPopulated()
    {
        $factory = new Factory(
            $this->_config, 
            MockModelWithQueryMethods\User::class
        );

        $query = $factory->query()
            ->isEnabled();

        $this->assertNull(
            $query->getSql() 
        );
    }
    */
}
