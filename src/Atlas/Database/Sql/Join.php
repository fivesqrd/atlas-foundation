<?php
namespace Atlas\Database\Sql;

class Join
{
    protected $_stack = array();

    public function inner($table, $alias, $on)
    {
        return $this->addToStack($table, $on, $alias);
    }

    public function left($table, $alias, $on)
    {
        return $this->addToStack($table, $on, $alias, 'LEFT');
    }

    public function assemble()
    {
        if (empty($this->_stack)) {
            return null;
        }

        $strings = array();

        foreach ($this->_stack as $join) {
            array_push($strings, $this->getJoinString($join));
        }

        return ' ' . implode(' ' , $strings);

    }

    private function getJoinString($params)
    {
        if (!array_key_exists('on', $params) || empty($params['on'])) {
            throw new Exception('Join statement requires ON clause');
        }

        return $this->_getTypeString($params)
            . "JOIN {$this->_getTableString($params)}"
            . "ON {$params['on']}";
    }

    public function isJoined($alias)
    {
        if (array_key_exists($alias, $this->_stack)) {
            return true;
        } 

        return false;
    }

    private function addToStack($table, $on, $alias = null, $type = null)
    {
        if ($this->isJoined($alias)) {
            return false; /* TODO: figure out the appropriate response */
        }

        $params = array(
            'table'   => $table,
            'alias'   => $alias,
            'type'    => $type,
            'on'      => $on,
            'columns' => null,
        );

        $alias = $this->_getAlias($params);

        $this->_stack[$alias] = $params;
    }

    protected function _getAlias($params)
    {
        if (!array_key_exists('alias', $params) || $params['alias'] === null) { 
            return $params['table'];
        }

        return $params['alias'];
    }

    protected function _getTableString($params)
    {
        return $params['table'] 
            . ' AS ' . $this->_getAlias($params)
            . ' ';
    }

    protected function _getTypeString($join)
    {
        if (!array_key_exists('type', $join) || $join['type'] === null) {
            return null;
        }

        return $join['type'] . ' ';
    }
}
