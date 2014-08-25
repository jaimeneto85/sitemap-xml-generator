<?php
class Itaro_Sitemaps_Block_Admin_New_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $store_array = array("" => $this->__("Choose an option"));
        $stores = Mage::app()->getStores();
        $sitemap_types = "";
        $sitemap_fields = "";
        $attribute_list = "";
        
        foreach( $stores as $store )
        {
            $store_array[$store->getId()] = $store->getName();  
        }

        $type_collection = Mage::getModel('sitemaps/type')->getCollection();
        $attributesCollection = Mage::getSingleton('eav/config')
        ->getEntityType(Mage_Catalog_Model_Product::ENTITY)
        ->getAttributeCollection()
        ->addSetInfo();

        foreach ($attributesCollection as $attribute)
        {
            $attribute_list[$attribute->getId()] = $attribute->getAttributeCode();
        }

        foreach ($type_collection as $type)
        {
            $sitemap_types[$type->getId()] = $type->getName();
        }
        
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('new_sitemap', array('legend' => Mage::helper('sitemaps')->__('Sitemap Details')));
        $fieldset2 = $form->addFieldset('sitemap_fields', array('legend' => Mage::helper('sitemaps')->__('XML TAGs')));
        
        $fieldset->addField('type', 'text', array(
            'name'      => 'type',
            'title'    => Mage::helper('sitemaps')->__('Profile'),
            'label'    => Mage::helper('sitemaps')->__('Profile'),
            'note' => $this->__('Digite no campo para escolher uma opção'),
            'maxlength' => '250',
            'required'  => true,
        ));

        $fieldset->addField('filename', 'text', array(
            'name'      => 'filename',
            'title'     => Mage::helper('sitemaps')->__('Filename'),
            'label'     => Mage::helper('sitemaps')->__('Filename'),
            'maxlength' => '250',
            'note'      => Mage::helper('adminhtml')->__('exemplo: sitemap.xml'),
            'required'  => true,
        ));
 
        $fieldset->addField('path', 'text', array(
            'name'      => 'path',
            'title'     => Mage::helper('sitemaps')->__('Path'),
            'label'     => Mage::helper('sitemaps')->__('Path'),
            'maxlength' => '250',
            'note'  	=> Mage::helper('adminhtml')->__('exemplo: sitemap/'),
            'required'  => true,
        ));                     
        
        $fieldset->addField('store_id', 'select', array(
            'name'     => 'store_id',
            'title'    => Mage::helper('sitemaps')->__('Store ID'),
            'label'    => Mage::helper('sitemaps')->__('Store ID'),
            'required' => true,
        ))->setValues($store_array);

        $fieldset2->addField('field_master_code', 'text', array(
            'name'     => 'field[master][code]',
            'title'    => Mage::helper('sitemaps')->__('Tag Principal'),
            'label'    => Mage::helper('sitemaps')->__('Tag Principal'),
            'note' => $this->__('< tag >'),
            'class' => 'default-tag',
            'required' => true
        ));

        $fieldset2->addField('field_product', 'text', array(
            'name'     => 'field[master][product][code]',
            'title'    => Mage::helper('sitemaps')->__('Tag do produto'),
            'label'    => Mage::helper('sitemaps')->__('Tag do produto'),
            'note' => $this->__('< tag >'),
            'value' => 'product',
            'class' => 'default-tag',
            'required' => true
        ));

        $fieldset2->addField('field_product_name', 'text', array(
            'name'     => 'field[default][product_name][code]',
            'title'    => Mage::helper('sitemaps')->__('Tag nome do produto'),
            'label'    => Mage::helper('sitemaps')->__('Tag nome do produto'),
            'note' => $this->__('< tag >'),
            'value' => 'name',
            'class' => 'clone_me default-tag',
            'required' => true
        ));

        $fieldset2->addField('field_product_price', 'text', array(
            'name'     => 'field[default][product_price][code]',
            'title'    => Mage::helper('sitemaps')->__('Tag preço do produto'),
            'label'    => Mage::helper('sitemaps')->__('Tag preço do produto'),
            'note' => $this->__('< tag >'),
            'value' => 'price',
            'class' => 'clone_me default-tag',
            'required' => true
        ));

        $fieldset2->addField('field_product_sku', 'text', array(
            'name'     => 'field[default][product_sku][code]',
            'title'    => Mage::helper('sitemaps')->__('Tag SKU do produto'),
            'label'    => Mage::helper('sitemaps')->__('Tag SKU do produto'),
            'note' => $this->__('< tag >'),
            'value' => 'sku',
            'class' => 'clone_me default-tag',
            'required' => true
        ));

        $fieldset2->addField('field_product_url', 'text', array(
            'name'     => 'field[default][product_url][code]',
            'title'    => Mage::helper('sitemaps')->__('Tag URL do produto'),
            'label'    => Mage::helper('sitemaps')->__('Tag URL do produto'),
            'note' => $this->__('< tag >'),
            'value' => 'url_path',
            'class' => 'clone_me default-tag',
            'required' => true
        ));

        $fieldset2->addField('field_product_image', 'text', array(
            'name'     => 'field[default][product_image][code]',
            'title'    => Mage::helper('sitemaps')->__('Tag imagem do produto'),
            'label'    => Mage::helper('sitemaps')->__('Tag imagem do produto'),
            'note' => $this->__('< tag >'),
            'value' => 'image',
            'class' => 'clone_me default-tag',
            'required' => true
        ));

        $fieldset2->addField('field_product_stock', 'text', array(
            'name'     => 'field[default][product_stock][code]',
            'title'    => Mage::helper('sitemaps')->__('Tag estoque do produto'),
            'label'    => Mage::helper('sitemaps')->__('Tag estoque do produto'),
            'note' => $this->__('< tag >'),
            'value' => 'stock',
            'class' => 'clone_me default-tag',
            'required' => true
        ));

        $fieldset2->addField('fields', 'text', array(
            'name'     => 'field[custom][1][code]',
            'title'    => Mage::helper('sitemaps')->__('1'),
            'label'    => Mage::helper('sitemaps')->__('Custom TAG 1'),
            'note' => $this->__('< tag >'),
            'class' => 'clone_me custom-tag',
        ));

        $fieldset->addField('type_select', 'select', array(
                'name'     => 'type_select',
                'style' => 'display:none',
                'class' => 'hidden type_select'
        ))->setValues($sitemap_types);

        $fieldset->addField('attribute_select', 'select', array(
                'name'     => 'attribute_select',
                'style' => 'display:none',
                'class' => 'hidden attribute_select'
        ))->setValues($attribute_list); 	
 
        $form->setMethod('post');
        
        $form->setUseContainer(true);
        
        $form->setId('edit_form');
        
        $form->setAction($this->getUrl('*/*/gen'));
 
        $this->setForm($form);
    }
}