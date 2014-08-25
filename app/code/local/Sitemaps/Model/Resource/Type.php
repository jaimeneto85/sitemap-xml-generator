<?php
 
class Itaro_Sitemaps_Model_Resource_Type extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('sitemaps/type', 'type_id');
    }
}