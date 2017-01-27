<?php
namespace Model\User;

class Mapper extends \Atlas\Model\Mapper
{
	protected $_alias = null;

	protected $_table = null;

	protected $_key = 'id';

	protected $_map = array();

	protected $_readOnly = array('id');

	public function createObject($row)
	{
		return new Entity($this->_populate($row);
	}

	public function createCollection($rows)
	{
		return new Collection($rows);
	}
}
