<?php

class Sitemaps_Block_Admin_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
 
        $this->_blockGroup = 'sitemaps';
        $this->_mode = 'edit';
        $this->_controller = 'admin';
 
        if($this->getRequest()->getParam($this->_objectId))
        {
            $sitemap = Mage::getModel('sitemaps/sitemap')->load($this->getRequest()->getParam($this->_objectId));
                                
            Mage::register('sitemaps', $sitemap);
        }
    }
 
    public function getHeaderText()
    {
        return Mage::helper('sitemaps')->__("Edit Sitemap '%s'", $this->htmlEscape(Mage::registry('sitemaps')->getFilename()));
    }
}