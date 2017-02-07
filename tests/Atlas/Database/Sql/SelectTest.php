<?php
use PHPUnit\Framework\TestCase;
use Atlas\Database\Sql;

/**
 * @covers \Atlas\Database\Sql\Select
 */
class SelectTest extends TestCase
{
    protected $_select;

    protected function setUp()
    {
        $this->_select = Sql\Select::factory();
    }

    public function testSimpleQueryIsReturningValidSql()
    {
        $this->assertEquals(
            'SELECT * FROM users',
            $this->_select->assemble('*', 'users') 
        );
    }

    public function testWhereClauseIsReturningValidSql()
    {

        $this->_select->where()->isEqual('email', 'me@mycompany.com');

        $this->assertEquals(
            'SELECT * FROM users WHERE (email = ?)',
            $this->_select->assemble('*', 'users')
        );
    }

    public function testJoinIsReturningValidSql()
    {
        $this->_select->join()->inner('accounts', 'a', 'u.account_id = a.id');

        $expected = 'SELECT * FROM users AS u JOIN accounts AS a ON u.account_id = a.id';

        $this->assertEquals(
            $expected, $this->_select->assemble('*', 'users AS u') 
        );
    }

    public function testMultipleJoinsAreReturningValidSql()
    {
        $this->_select->join()->inner('accounts', 'a', 'u.account_id = a.id');
        $this->_select->join()->inner('roles', 'r', 'u.role_id = r.id');

        $expected = 'SELECT * FROM users AS u JOIN accounts AS a ON u.account_id = a.id JOIN roles AS r ON u.role_id = r.id';

        $this->assertEquals(
            $expected, $this->_select->assemble('*', 'users AS u')
        );
    }

    public function testOrderClauseIsReturningValidSql()
    {
        $this->assertEquals(
            'SELECT * FROM users ORDER BY id',
            $this->_select->order('id')->assemble('*', 'users') 
        );
    }

    public function testLimitClauseIsReturningValidSql()
    {
        $this->assertEquals(
            'SELECT * FROM users ORDER BY id LIMIT ?',
            $this->_select->order('id')->limit(10)->assemble('*', 'users') 
        );
    }

    public function testLimitClauseWithOffsetIsReturningValidSql()
    {
        $this->assertEquals(
            'SELECT * FROM users ORDER BY id LIMIT ?,?',
            $this->_select->order('id')->limit(10, 5)->assemble('*', 'users') 
        );
    }
}
