<?php
use PHPUnit\Framework\TestCase;
use Atlas\Database\Sql;

/**
 * @covers \Atlas\Database\Sql\Fetch
 */
class FetchTest extends TestCase
{

    public function testGetSqlIsReturningValidSql()
    {
        $select = Sql\Select::factory();

        $select->where()->isEqual('email', 'me@mycompany.com', 'u');

        $statement = new Sql\Statement(
            null, 'users', 'u', $select
        );

        $this->assertEquals(
            'SELECT `u`.* FROM `users` AS `u` WHERE (`u`.`email` = ?)',
            $statement->assemble()
        );
    }
}
