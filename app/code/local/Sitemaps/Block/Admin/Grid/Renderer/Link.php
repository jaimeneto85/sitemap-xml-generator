<?php
class Sitemaps_Block_Admin_Grid_Renderer_Link extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Prepare link to display in grid
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $fileName = preg_replace('/^\//', '', $row->getPath() . $row->getFilename());
        
        $url = $this->htmlEscape(Mage::app()->getStore($row->getStoreId())->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $fileName);

        if (file_exists(BP . DS . $fileName)) {
            return sprintf('<a href="%1$s" target="_blank">%1$s</a>', $url);
        }
        return $url;
    }

}