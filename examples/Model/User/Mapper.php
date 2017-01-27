<?php
namespace Model\User;

class Mapper extends \Atlas\Model\Mapper
{
	protected $_alias = null;

	protected $_table = null;

	protected $_key = 'id';

	protected $_map = array();

	protected $_readOnly = array('id');

	public function getObject($row)
	{
		return new \Model\User\Entity($rows);
	}
	
	public function getCollection($rows)
	{
		return new \Model\User\Collection($rows);
	}
	
}