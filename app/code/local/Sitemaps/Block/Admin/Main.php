<?php
class Sitemaps_Block_Admin_Main extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    
    public function __construct()
    {
        parent::__construct();
        
        $this->_headerText = Mage::helper('sitemaps')->__('Itaro Sitemaps');
        
        $this->_blockGroup = 'sitemaps';
        
        $this->_controller = 'admin_main';
        
        $this->_addButton('add', array(
            'label'   => Mage::helper('sitemaps')->__('Adicionar Sitemap'),
            'onclick' => "setLocation('{$this->getUrl('*/*/new')}')",
            'class'   => 'add'
        ));
    }
}