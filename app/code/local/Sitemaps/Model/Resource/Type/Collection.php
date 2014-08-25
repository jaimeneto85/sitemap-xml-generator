<?php
 
class Itaro_Sitemaps_Model_Resource_Type_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct() {
       $this->_init('sitemaps/type');
    }
}