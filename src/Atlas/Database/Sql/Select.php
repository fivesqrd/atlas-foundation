<?php
namespace Atlas\Database\Sql;

class Select
{
    protected $_join;

    protected $_where;

    protected $_order;

    protected $_limit;

    public static function factory($alias = null)
    {
        return new self(
            new Join(), new Where($alias), new Order(), new Limit()
        );
    }

    public function __construct(Join $join, Where $where, Order $order, Limit $limit)
    {
        $this->_join  = $join; 
        $this->_where = $where; 
        $this->_order = $order; 
        $this->_limit = $limit; 
    }

    public function assemble($what, $where)
    {
        return "SELECT {$what} FROM {$where}"
            . $this->_join->assemble()
            . $this->_where->assemble()
            . $this->_order->assemble()
            . $this->_limit->assemble();
    }

    public function getBoundValues()
    {
        return $this->_where->getBoundValues();
    }

    public function join()
    {
        return $this->_join;
    }

    public function where()
    {
        return $this->_where;
    }

    public function limit($count, $offset = null)
    {
        $this->_limit->set($count, $offset);
        return $this;
    }

    public function order($spec)
    {
        $this->_order->set($spec);
        return $this;
    }

}
