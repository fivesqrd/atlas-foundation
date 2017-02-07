<?php
namespace MockModelBarebones\User;

class Named extends \Atlas\Model\Named
{

	protected function _factory()
	{
		return new Query($this->_adapter);
	}
}
