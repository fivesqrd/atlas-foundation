<?php
class Atlas_Key
{
    public static function encode(Atlas_Model_Entity $model)
    {
        $mask = new Atlas_Mask_Base36();
         
        $class = get_class($model);
        $key = array($class, $mask->encode($model->getId()));
         
        return base64_encode(json_encode($key));
    }
    
    public static function decode($key)
    {
        $mask = new Atlas_Mask_Base36();
        
        $decoded = json_decode(base64_decode($key), true);
        
        if (!is_array($decoded)) {
            throw new Exception('Cannot unlock the key provided. Malformed key.');
        }
        
        list($class, $id) = $decoded;
        
        return array($class, $mask->decode($id));
    }
    
    public static function decodeAndFetch($key)
    {
        list($class, $id) = self::decode($key);
         
        $model = $class::query()->fetchOne($id);
    
        if (!$model->getId()) {
            throw new Exception('Could not find/unlock the record requested with the provided key');
        }
    
        return $model;
    }
}
