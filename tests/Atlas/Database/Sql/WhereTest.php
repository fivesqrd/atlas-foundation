<?php
use PHPUnit\Framework\TestCase;

/**
 * @covers \Atlas\Database\Sql\Where
 */
class WhereTest extends TestCase
{
    public function testEmptyStackIsReturningNull()
    {
        $where = new Atlas\Database\Sql\Where();

        $this->assertNull(
            $where->assemble() 
        );
    }

    public function testIsEqualClauseIsReturningValidSql()
    {
        $where = new Atlas\Database\Sql\Where();

        $where->isEqual('email', 'me@mycompany.com');

        $this->assertEquals(
            ' WHERE (`email` = ?)',
            $where->assemble() 
        );
    }

    public function testClauseWithAliasIsReturningValidSql()
    {
        $where = new Atlas\Database\Sql\Where();

        $where->isEqual('email', 'me@mycompany.com', 'u');

        $this->assertEquals(
            ' WHERE (`u`.`email` = ?)',
            $where->assemble() 
        );
    }

    public function testMultipleClausesAreReturningAndClause()
    {
        $where = new Atlas\Database\Sql\Where();

        $where->isEqual('email', 'me@mycompany.com')
            ->isEqual('enabled', 1);

        $this->assertEquals(
            ' WHERE (`email` = ?) AND (`enabled` = ?)',
            $where->assemble() 
        );
    }

    public function testMultipleClausesAreReturningValidValues()
    {
        $where = new Atlas\Database\Sql\Where();

        $where->isEqual('email', 'me@mycompany.com')
            ->isEqual('enabled', 1);

        $this->assertEquals(
            array('me@mycompany.com', 1),
            $where->getBoundValues() 
        );
    }

    public function testIsInClausesAreReturningValidValues()
    {
        $where = new Atlas\Database\Sql\Where();

        $where->isEqual('email', 'me@mycompany.com')
            ->isIn('status', array('active', 'trial'));

        $this->assertEquals(
            array('me@mycompany.com', 'active', 'trial'),
            $where->getBoundValues() 
        );
    }

    public function testAndClauseIsReturningValidSql()
    {
        $where = new Atlas\Database\Sql\Where();

        $where->isEqual('email', 'me@mycompany.com')
            ->and('enabled = ? or login is null', array(0));

        $this->assertEquals(
            ' WHERE (`email` = ?) AND (enabled = ? or login is null)',
            $where->assemble() 
        );
    }
}
