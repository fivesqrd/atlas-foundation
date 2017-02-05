<?php
use PHPUnit\Framework\TestCase;

/**
 * @covers \Atlas\Factory
 */
class SelectTest extends TestCase
{
    public function testSimpleQueryIsReturningValidSql()
    {
        $select = new Atlas\Database\Sql\Select();

        $this->assertEquals(
            'SELECT * FROM users',
            $select->assemble('*', 'users') 
        );
    }

    public function testWhereClauseIsReturningValidSql()
    {
        $select = new Atlas\Database\Sql\Select();

        $select->where()->isEqual('email', 'me@mycompany.com');

        $this->assertEquals(
            'SELECT * FROM users WHERE (email = ?)',
            $select->assemble('*', 'users')  . $select->where()->assemble()
        );
    }

    public function testJoinIsReturningValidSql()
    {
        $select = new Atlas\Database\Sql\Select();
        $select->join('accounts', 'a')
            ->on('u.account_id = a.id');

        $expected = 'SELECT * FROM users AS u JOIN accounts AS a ON u.account_id = a.id';

        $this->assertEquals(
            $expected, $select->assemble('*', 'users AS u') 
        );
    }

    public function testMultipleJoinsAreReturningValidSql()
    {
        $select = new Atlas\Database\Sql\Select();
        $select->join('accounts', 'a')
            ->on('u.account_id = a.id');
        $select->join('roles', 'r')
            ->on('u.role_id = r.id');

        $expected = 'SELECT * FROM users AS u JOIN accounts AS a ON u.account_id = a.id JOIN roles AS r ON u.role_id = r.id';

        $this->assertEquals(
            $expected, $select->assemble('*', 'users AS u')
        );
    }
}
