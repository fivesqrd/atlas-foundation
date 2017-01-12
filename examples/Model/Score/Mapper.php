<?php
namespace Application\Model\Score;

use Application\Model as Model;

class Mapper extends \Atlas_Model_Mapper
{
    protected $_alias = 's';

    protected $_table = 'scores';

    protected $_key = 'id';

    protected $_map = array(
        '_id'           => 'id',
        '_metric'       => 'metric',
        '_value'        => 'value',
        '_period'       => 'period',
        '_timestamp'    => 'timestamp',
    );
    
    protected $_readOnly = array('id');
    
    public function getObject($row)
    {
        return new Model\Score\Entity($this->_populate($row));
    }

    public function getCollection($rows)
    {
        return new Model\Score\Collection($rows);
    }
}
