<?php
class Itaro_Sitemaps_Block_Admin_New extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
 
        $this->_blockGroup = 'sitemaps';
        $this->_mode = 'new';
        $this->_controller = 'admin';
    }
 
    public function getHeaderText()
    {
        return Mage::helper('sitemaps')->__('Add New Sitemap');
    }
}