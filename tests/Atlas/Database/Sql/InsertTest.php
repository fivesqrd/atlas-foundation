<?php
use PHPUnit\Framework\TestCase;

/**
 * @covers \Atlas\Factory
 */
class InsertTest extends TestCase
{
    public function testSimpleQueryIsReturningValidSql()
    {
        $data = array(
            'name'    => 'Jack',
            'surname' => 'Sparrow',
            'email'   => 'me@mycompany.com'
        );
    
        $insert = new Atlas\Database\Sql\Insert('users', $data);

        $this->assertEquals(
            'INSERT INTO users (name, surname, email) VALUES (?, ?, ?)',
            $insert->assemble() 
        );

        $this->assertEquals(
            array_values($data),
            $insert->getBoundValues() 
        );
    }
}
