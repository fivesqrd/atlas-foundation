<?php
require_once('TestAsset/MockModelBarebones/User.php');
require_once('TestAsset/MockModelBarebones/User/Mapper.php');
require_once('TestAsset/MockModelBarebones/User/Query.php');
require_once('TestAsset/MockModelBarebones/User/Named.php');

use PHPUnit\Framework\TestCase;
use Atlas\Query;
use MockModelBarebones as Model;


/**
 * @covers \Atlas\Factory
 */
class QueryTest extends TestCase
{
    protected $_config = array(
        'driver'   => 'Pdo_Mysql',
        'dbname'   => 'tact',
        'username' => 'username',
        'password' => 'password',
        'host'     => '192.168.254.10'
    );

    public function testFetchMethodIsReturningValidObject()
    {
        $adapter = new \Zend_Db_Adapter_Pdo_Mysql($this->_config);

        $query = new Model\User\Query(
            new Model\User\Mapper(), 
            new \Zend_Db_Select($adapter)
        ); 

        $this->assertInstanceOf(
            Query\Fetch::class,
            $query->fetch() 
        );
    }

    public function testSqlIsEmpty()
    {
        $adapter = new \Zend_Db_Adapter_Pdo_Mysql($this->_config);

        $query = new Model\User\Query(
            new Model\User\Mapper(), 
            new \Zend_Db_Select($adapter)
        ); 

        $this->assertNull(
            $query->getSql() 
        );
    }
}
