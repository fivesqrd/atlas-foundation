<?php
namespace Application\Model\Score;

class Collection extends \Atlas_Model_Collection
{
    public function targetClass()
    {
        return '\\Application\\Model\\Score';
    }
}
