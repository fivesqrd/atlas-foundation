<?php
use PHPUnit\Framework\TestCase;
use Atlas\Database\Sql;

/**
 * @covers \Atlas\Database\Sql\Select
 */
class InjectionTest extends TestCase
{
    protected $_select;

    protected $_mapper;

    protected $_adapter;

    protected function setUp()
    {
        $this->_mapper = $this->getMockBuilder('User\Mapper')
            ->setMethods(array('getTable','getAlias'))
            ->getMock();

        $this->_mapper->method('getAlias')->willReturn('u');
        $this->_mapper->method('getTable')->willReturn('users');
        $this->_select = Sql\Select::factory(
            $this->_mapper->getAlias()
        );

        $this->_adapter = new \Pdo('sqlite::memory');
        $this->_adapter->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);
        $this->_adapter->query('CREATE TABLE users (id integer primary key autoincrement, email varchar(255))');
    }

    protected function tearDown()
    {
        $this->_adapter->query('DROP TABLE users');
        $this->_adapter = null;
    }

    public function testOrClauseIsEscaped()
    {
        $this->_select->where()
            ->isEqual('email', "me@mycompany.com' OR 1==1");

        $statement = new Sql\Statement(
            null, 'users', 'u', $this->_select
        ); 

        $this->assertEquals(
            'SELECT `u`.* FROM `users` AS `u` WHERE (`u`.`email` = ?)',
            $statement->assemble()
        );
    }

    public function testIdentifierIsEscaped()
    {
        $this->_select->where()
            ->isEqual('`email`', "me@mycompany.com' OR 1==1");

        $statement = new Sql\Statement(
            null, 'users', 'u', $this->_select
        ); 

        $this->assertEquals(
            'SELECT `u`.* FROM `users` AS `u` WHERE (`u`.```email``` = ?)',
            $statement->assemble()
        );
    }

    public function testTableNameWithDash()
    {
        $this->expectException(
            'Atlas\Database\Exception', 
            'Table may contain only alphanumerics or underscores, and may not begin with a digit'
        );

        $this->_select->where()
            ->isEqual('`email`', "me@mycompany.com' OR 1==1");

        $statement = new Sql\Statement(
            null, 'user-list', 'u', $this->_select
        ); 

        $statement->assemble();
    }

    public function testTableNameBeginningWithNumber()
    {
        $this->expectException(
            'Atlas\Database\Exception', 
            'Table may contain only alphanumerics or underscores, and may not begin with a digit'
        );

        $this->_select->where()
            ->isEqual('`email`', "me@mycompany.com' OR 1==1");

        $statement = new Sql\Statement(
            null, '0user', 'u', $this->_select
        ); 

        $statement->assemble();
    }
}
