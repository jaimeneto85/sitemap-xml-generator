<?php
 
class Sitemaps_Model_Resource_Sitemap_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct() {
       $this->_init('sitemaps/sitemap');
    }
}