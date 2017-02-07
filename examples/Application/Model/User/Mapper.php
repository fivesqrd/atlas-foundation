<?php
namespace Application\Model\User;

class Mapper extends \Atlas\Model\Mapper
{
	protected $_alias = null;

	protected $_table = null;

	protected $_key = 'id';

	protected $_map = array(
		'_id' => 'id'
	);

	protected $_readOnly = array('id');


	public function getEntity($row)
	{
		return new Entity($this->_populate($row));
	}

	public function getCollection($rows)
	{
		return new Collection($rows, $this);
	}
}