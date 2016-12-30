<?php
class Atlas_Observer_ModelAuditLog extends Atlas_Model_Observer
{
    protected $_session;

    protected $_db;

    public function __construct($connection)
    {
        $this->_db = $connection;
        $this->_session = new Zend_Session_Namespace();
    }

    protected function _createHook()
    {
        $data = array(
            'object_type'=> get_class($this->_model),
            'object_key' => $this->_model->getId(),
            'user_id'    => isset($this->_session->user) ? $this->_session->user : null,
            'timestamp'  => date("Y-m-d H:i:s"),
        );
        $this->_db->insert('_model_create_log', $data);
    }

    protected function _changeHook($property, $before, $after)
    {
        $data = array(
            'object_type'=> get_class($this->_model),
            'object_key' => $this->_model->getId(),
            'user_id'    => isset($this->_session->user) ? $this->_session->user : null,
            'timestamp'  => date("Y-m-d H:i:s"),
            'property'   => $property,
            'before'     => $this->_getSafeValue($before),
            'after'      => $this->_getSafeValue($after)
        );
        $this->_db->insert('_model_update_log', $data);
    }

    protected function _getSafeValue($value)
    {
        if (is_array($value) || is_object($value)) {
            return json_encode($value);
        }
        return $value;
    }

    protected function _deleteHook()
    {
        $data = array(
            'object_type'=> get_class($this->_model),
            'object_key' => $this->_model->getId(),
            'user_id'    => isset($this->_session->user) ? $this->_session->user : null,
            'timestamp'  => date("Y-m-d H:i:s"),
        );
        $this->_db->insert('_model_delete_log', $data);
    }
}