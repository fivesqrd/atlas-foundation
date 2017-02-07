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
        $select = new Sql\Select(new Sql\Where());

        $select->where()->isEqual('email', 'me@mycompany.com', 'u');

        $mapper = $this->getMockBuilder('User\Mapper')
            ->setMethods(array('getTable','getAlias'))
            ->getMock();

        $mapper->method('getAlias')->willReturn('u');
        $mapper->method('getTable')->willReturn('users');

        $fetch = new Atlas\Database\Sql\Fetch(
            null, $mapper, $select
        );

        $this->assertEquals(
            'SELECT u.* FROM users AS u WHERE (u.email = ?)',
            $fetch->getSql()
        );
    }
}
