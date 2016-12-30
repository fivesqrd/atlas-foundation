<?php
abstract class Atlas_Model_Relation
{
    /**
     * @var Atlas_Model
     */
    protected $_model;

    protected $_mappers;

    public function __construct($model, $mappers = array())
    {
        $this->_model = $model;
        $this->_mappers = $mappers;
    }

    /**
     * @param string $name
     * @return Atlas_Model_Mapper
     * @throws Exception
     */
    protected function _getMapper($name)
    {
        if (!array_key_exists($name, $this->_mappers)) {
            throw new Exception(get_class($this) . ' does not have required ' . $name . ' mapper loaded');
        }

        return $this->_mappers[$name];
    }
    
    /**
     * @return Atlas_Entity_CreateLog
     */
    public function createLog()
    {
        return Atlas_Entity_CreateLog::query()->bespoke()
            ->modelIs($this->_model)
            ->fetchOne();
    }
    
    /**
     * @return Atlas_Entity_UpdateLog_Collection
     */
    public function allUpdateLogs()
    {
        return Atlas_Entity_UpdateLog::query()->bespoke()
            ->modelIs($this->_model)
            ->fetchAll();
        
    }
    
    /**
     * @return Atlas_Entity_UpdateLog
     */
    public function lastUpdateLog()
    {
        return Atlas_Entity_UpdateLog::query()->bespoke()
            ->modelIs($this->_model)
            ->sortBy('id desc')
            ->fetchOne();
    }
    
    /**
     * @param string $property
     * @return Atlas_Entity_UpdateLog
     */
    public function lastUpdateLogByProperty($property)
    {
        return Atlas_Entity_UpdateLog::query()->bespoke()
            ->modelIs($this->_model)
            ->propertyIs($property)
            ->sortBy('id desc')
            ->fetchOne();
    }
    
    /**
     * @return Atlas_Entity_DeleteLog
     */
    public function deleteLog()
    {
        return Atlas_Entity_DeleteLog::query()->bespoke()
            ->modelIs($this->_model)
            ->fetchOne();
    }
}
