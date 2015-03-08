<?php
 
class Sitemaps_Model_Resource_Sitemap extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('sitemaps/sitemap', 'sitemap_id');
    }
}