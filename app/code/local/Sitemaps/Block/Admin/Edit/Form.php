<?php
class Itaro_Sitemaps_Block_Admin_Edit_Form extends Mage_Adminhtml_Block_Widget_Form 
{
    protected function _prepareForm() {
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

        $data = Mage::registry('sitemaps')->getData();
        $profile = Mage::getModel('sitemaps/type')->load($data['type']);
        $profileName = $profile->getName();
        $fields = json_decode($profile->getFields());

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('edit_sitemap', array('legend' => Mage::helper('sitemaps')->__('Sitemap Details')));
        $fieldset2 = $form->addFieldset('sitemap_fields', array('legend' => Mage::helper('sitemaps')->__('XML TAGs')));

        $fieldset->addField('sitemap_id', 'hidden', array(
            'name' => 'sitemap_id',
            'value' => $data["sitemap_id"]
        ));

        $fieldset->addField('type', 'text', array(
            'name'      => 'type',
            'title'    => Mage::helper('sitemaps')->__('Profile'),
            'label'    => Mage::helper('sitemaps')->__('Profile'),
            'note' => $this->__('Digite no campo para escolher uma opção'),
            'maxlength' => '250',
            'required'  => true,
            'value' => $profileName
        ));

        $fieldset->addField('filename', 'text', array(
            'name'      => 'filename',
            'title'     => Mage::helper('sitemaps')->__('Filename'),
            'label'     => Mage::helper('sitemaps')->__('Filename'),
            'maxlength' => '250',
            'note'      => Mage::helper('adminhtml')->__('exemplo: sitemap.xml'),
            'required'  => true,
            'value' => $data['filename']
        ));
 
        $fieldset->addField('path', 'text', array(
            'name'      => 'path',
            'title'     => Mage::helper('sitemaps')->__('Path'),
            'label'     => Mage::helper('sitemaps')->__('Path'),
            'maxlength' => '250',
            'note'      => Mage::helper('adminhtml')->__('exemplo: sitemap/'),
            'required'  => true,
            'value' => $data['path']
        ));                     
        
        $fieldset->addField('store_id', 'select', array(
            'name'     => 'store_id',
            'title'    => Mage::helper('sitemaps')->__('Store ID'),
            'label'    => Mage::helper('sitemaps')->__('Store ID'),
            'required' => true,
        ))->setValues($store_array);

        foreach ($fields as $key => $value)
        {
            if($key === 'master')
            {
                foreach($value as $k => $ff)
                {
                    if($k !== 'product')
                    {
                        $fieldset2->addField('field_master_code', 'text', array(
                            'name'     => 'field[master][code]',
                            'title'    => Mage::helper('sitemaps')->__('Tag Principal'),
                            'label'    => Mage::helper('sitemaps')->__('Tag Principal'),
                            'note' => $this->__('< tag >'),
                            'class' => 'default-tag',
                            'required' => true,
                            'value' => $value->code
                        ));
                    }
                    else
                    {
                        $fieldset2->addField('field_'.$k.'_code', 'text', array(
                            'name'     => 'field[master]['.$k.'][code]',
                            'title'    => self::_getLabel($k),
                            'label'    => self::_getLabel($k),
                            'note' => $this->__('< tag >'),
                            'value' => $ff->code,
                            'class' => 'default-tag',
                            'required' => true
                        ));
                    }
                }
            }

            if($key === 'default')
            {
                foreach ($value as $k => $v)
                {
                    $class = "";
                    
                    $field_code = $v->code;
                    $field_value = $v->value;
                    $class = 'next_to_me ';

                    $label = self::_getLabel($k);

                    $fieldset2->addField('field_'.$k.'_code', 'text', array(
                        'name'     => 'field[default]['.$k.'][code]',
                        'title'    => $label,
                        'label'    => $label,
                        'note' => $this->__('< tag >'),
                        'value' => $field_code,
                        'class' => $class.'default-tag',
                        'required' => true
                    ));
                    
                    $fieldset2->addField('field_'.$k.'_value', 'text', array(
                        'name'     => 'field[default]['.$k.'][value]',
                        'title'    => $label,
                        'label'    => $label,
                        'note' => $this->__('value'),
                        'value' => $field_value,
                        'class' => 'move_me default-tag',
                        'required' => true
                    ));
                }
            }

            if($key === 'custom')
            {
                foreach ($value as $k => $v)
                {
                    $k === 'product' ? $field_value = $k : $field_value = $v->code;

                    $fieldset2->addField('fields_code'.$k, 'text', array(
                        'name'     => 'field[custom]['.$k.'][code]',
                        'title'    => Mage::helper('sitemaps')->__($k),
                        'label'    => Mage::helper('sitemaps')->__('Custom TAG '.$k),
                        'note' => $this->__('< tag >'),
                        'class' => 'next_to_me custom-tag',
                        'value' => $v->code
                    ));

                    $fieldset2->addField('fields_value'.$k, 'text', array(
                        'name'     => 'field[custom]['.$k.'][value]',
                        'title'    => Mage::helper('sitemaps')->__($k),
                        'label'    => Mage::helper('sitemaps')->__('Custom TAG '.$k),
                        'note' => $this->__('value'),
                        'class' => 'move_me custom-tag',
                        'value' => $v->value
                    ));
                }
            }
        }

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
        $form->setAction($this->getUrl('*/*/save'));

        $this->setForm($form);
    }

    protected static function _getLabel($field)
    {
        switch ($field)
        {
            case 'product':
                return Mage::helper('sitemaps')->__('Tag do produto');
            break;

            case 'product_name':
                return Mage::helper('sitemaps')->__('Tag nome do produto');
            break;

            case 'product_price':
                return Mage::helper('sitemaps')->__('Tag preço do produto');
            break;

            case 'product_sku':
                return Mage::helper('sitemaps')->__('Tag SKU do produto');
            break;

            case 'product_url':
                return Mage::helper('sitemaps')->__('Tag URL do produto');
            break;

            case 'product_image':
                return Mage::helper('sitemaps')->__('Tag imagem do produto');
            break;

            case 'product_stock':
                return Mage::helper('sitemaps')->__('Tag estoque do produto');
            break;
            
            default:
                return false;
        }
    }
}