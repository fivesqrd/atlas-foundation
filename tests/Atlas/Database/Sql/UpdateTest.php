<?php
use PHPUnit\Framework\TestCase;
use Atlas\Database\Sql;

/**
 * @covers \Atlas\Database\Sql\Update
 */
class UpdateTest extends TestCase
{
    protected $_data = array(
        'name'    => 'Jack',
        'surname' => 'Sparrow',
        'email'   => 'me@mycompany.com'
    );
    
    public function testSimpleQueryIsReturningValidSqlTemplate()
    {
        $update = new Sql\Update(
            'users', $this->_data, (new Sql\Where())
        );

        $this->assertEquals(
            'UPDATE users SET name = ?, surname = ?, email = ?',
            $update->assemble() 
        );
    }

    public function testSimpleQueryIsReturningValidValues()
    {
        $update = new Sql\Update(
            'users', $this->_data, (new Sql\Where())
        );

        $this->assertEquals(
            array_values($this->_data),
            $update->getBoundValues() 
        );
    }

    public function testWhereClauseIsReturningValidSqlTemplate()
    {
        $update = new Sql\Update(
            'users', $this->_data, (new Sql\Where())->isEqual('id', 1)
        );

        $this->assertEquals(
            'UPDATE users SET name = ?, surname = ?, email = ? WHERE (id = ?)',
            $update->assemble() 
        );
    }

    public function testWhereClauseIsReturningValidValues()
    {
        $update = new Sql\Update(
            'users', $this->_data, (new Sql\Where())->isEqual('id', 1)
        );

        $this->assertEquals(
            array_values($this->_data),
            $update->getBoundValues() 
        );
    }
}
