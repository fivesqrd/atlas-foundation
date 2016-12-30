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
    
    public function createObject($row)
    {
        return new Model\Score\Entity($this->_populate($row));
    }

    public function createCollection($rows)
    {
        return new Model\Score\Collection($rows);
    }

    public function save(Model\Score\Entity $entity)
    {
        print_r($this->_extract($entity));
        return $this->_save('scores', $this->_extract($entity), $entity);
    }
    
    public function fetch($id)
    {
        return $this->_fetch('scores', $id);
    }

    public function delete($model)
    {
        return $this->_delete('scores', $model);
    }
}
