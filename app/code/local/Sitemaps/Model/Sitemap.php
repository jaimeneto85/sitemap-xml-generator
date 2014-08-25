<?php
/*
* Itaro Sitemaps XML Generator
* Desenvolvido por @jaimeneto85 + @wellbishopt
* Projeto @jaimeneto85
*
* Versao 0.1
*/
class Itaro_Sitemaps_Model_Sitemap extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('sitemaps/sitemap');
    }

    public static function updateall()
    {
        $sitemapModel = Mage::getModel('sitemaps/sitemap');
        $typeModel = Mage::getModel('sitemaps/type');
        $sitemapCollection = Mage::getResourceModel('sitemaps/sitemap_collection');

        foreach ($sitemapCollection as $sitemap)
        {
            $type = $typeModel->load($sitemap->getType());
            $fields = json_decode($type->getFields());
            $params = array();

            $params['sitemap_id'] = $sitemap->getId();
            $params['type'] = $type->getName();
            $params['filename'] = $sitemap->getFilename();
            $params['path'] = $sitemap->getPath();

            foreach ($fields as $key => $value)
            {
                if($key === 'master')
                {
                    foreach($value as $k => $ff)
                    {
                        if($k !== 'product')
                        {
                            $params['field']['master']['code'] = $value->code;
                        }
                        else
                        {
                            $params['field']['master']['product']['code'] = $ff->code;
                        }
                    }
                }

                if($key === 'default')
                {
                    foreach ($value as $k => $v)
                    {
                        $params['field']['default'][$k]['code'] = $v->code;
                        $params['field']['default'][$k]['value'] = $v->value;
                    }
                }

                if($key === 'custom')
                {
                    foreach ($value as $k => $v)
                    {
                        $params['field']['custom'][$k]['code'] = $v->code;
                        $params['field']['custom'][$k]['value'] = $v->value;
                    }
                }
            }

            $sitemapModel->genSitemap($params, 'edit');
        }
    }

    public function genSitemap($params, $saveMode = NULL)
    {
        $product_collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED))
            ->addAttributeToFilter('price', array('neq' => NULL));

        $base = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        $filename = $params['filename'];
        $timestamp = "\n<!-- Generated at ". gmdate('Y-m-d\TH:i:s', time()) ."-->";
        $masterTag = $params['field']['master']['code'];
        $producTag = $params['field']['master']['product']['code'];

        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
        $xml .= sprintf("\n%s", $timestamp);
        $xml .= sprintf("\n<%s>", $masterTag);

        $io = new Varien_Io_File();
        $io->setAllowCreateFolders(true);
        $io->open(array('path' => Mage::getBaseDir().'/'.$params['path']));
        $io->streamOpen(Mage::getBaseDir()."/".$params['path'].$filename);

        $io->streamWrite($xml);
        $xml = "";
        
        foreach ($product_collection as $product)
        {
            $xml .= sprintf("\n\t<%s>", $producTag);

            foreach ($params['field'] as $fieldType => $field) 
            {
                switch($fieldType)
                {
                    case 'default':
                    case 'custom':
                        foreach ($field as $param)
                        {
                            $code = $param['code'];
                            $value = self::splitAndSet($param['value'], $product);

                            $xml .= sprintf("\n\t\t<%s>%s</%s>", $code, $value, $code);
                        }
                    break;
                }           
            }

            $xml .= sprintf("\n\t</%s>", $producTag);

            $io->streamWrite($xml);     
            $xml = "";
        }

        $xml .= sprintf("\n</%s>", $masterTag);

        $io->streamWrite($xml);

        if($io->streamClose())
        {
            unset($product_collection);

            $configType = self::_getTypeId($params['type']);
            $typeModel = Mage::getModel('sitemaps/type')->load($configType['id']);
            $typeModel->setFields(json_encode($params['field']))->save();

            if(is_null($saveMode))
            {
                $sitemaps = Mage::getModel('sitemaps/sitemap')
                ->setFilename($filename)
                ->setPath($params['path'])
                ->setLink(self::_getLink($params['path'], $filename))
                ->setType($configType['id'])
                ->setData('created_at', date('Y-m-d H:i:s'))
                ->setData('updated_at', date('Y-m-d H:i:s'));
            }
            else
            {
                if($saveMode === 'edit')
                {
                    $sitemaps = Mage::getModel('sitemaps/sitemap')->load($params['sitemap_id'])
                    ->setFilename($filename)
                    ->setPath($params['path'])
                    ->setLink(self::_getLink($params['path'], $filename))
                    ->setType($configType['id'])
                    ->setData('updated_at', date('Y-m-d H:i:s'));
                }
            }

            if($sitemaps->save())
            {
                return true;
            }
        }
    }

    private static function isProductTag($field)
    {
        $keys = array_keys($field);

        return $keys[0] === 'product' ? true : false;
    }

    private static function splitAndSet($fieldValue, $product)
    {
        $sliced = split(' ', $fieldValue);

        $value = '';

        if(count($sliced))
        {
            foreach ($sliced as $slice)
            {
                $value = $value.' '.self::checkFieldValue($slice, $product);
            }
        }
        else
        {
            $value = checkFieldValue($fieldValue, $product);
        }

        $value = self::sanitizeWhiteSpaces($value);

        return (string) $value;
    }

    private static function checkFieldValue($fieldValue, $product)
    {
        $value = '';
        
        if(self::isVar($fieldValue))
        {
            if(self::isExpression($fieldValue))
            {
                $value = self::calc($fieldValue, $product);
            }
            else
            {
                $attribute = self::extractAttribute($fieldValue);
                $value = self::getAttributeValue($attribute, $product);
            }
        }
        else
        {
            $value = $fieldValue;
        }

        if($side = self::needConcat($fieldValue))
        {
            $value = self::concat($value, $fieldValue, $side);
        }

        return (string) $value;
    }

    private static function needConcat($value)
    {
        if(preg_match('/[a-zA-Z0-9]+(\$)?\{{2}\w*\}{2}(\$)?[a-zA-Z0-9]+/', $value) && !self::isExpression($value))
        {
            return 'both';
        }
        elseif(preg_match('/[a-zA-Z0-9]+(\$)?\{{2}\w*\}{2}/', $value) && !self::isExpression($value))
        {
            return 'left';
        }
        elseif(preg_match('/\{{2}\w*\}{2}(\$)?[a-zA-Z0-9]+/', $value) && !self::isExpression($value))
        {
            return 'right';
        }
        else
        {
            return false;
        }
    }

    private static function concat($value, $fieldValue, $side)
    {
        switch($side)
        {
            case 'left':
                $concat = preg_replace('/(.*)\{{2}.*\}{2}/', '$1', $fieldValue);
                return $concat.$value;
            break;

            case 'right':
                $concat = preg_replace('/\{{2}.*\}{2}(.*)/', '$1', $fieldValue);
                return $value.$concat;
            break;

            case 'both':
                $left = preg_replace('/(.*)\{{2}.*\}{2}(.*)/', '$1', $fieldValue);
                $right = preg_replace('/(.*)\{{2}.*\}{2}(.*)/', '$2', $fieldValue);

                return $left.$value.$right;
            break;
        }
    }

    private static function sanitizeWhiteSpaces($string)
    {
        $cleanStringBeginning = preg_replace('/^\s+/', '', $string);
        $cleanStringMiddle = preg_replace('/\s{2,}/', ' ', $cleanStringBeginning);
        $cleanStringEnding = preg_replace('/\s+$/', '', $cleanStringMiddle);

        $clearedString = $cleanStringEnding;

        return $clearedString;
    }

    private static function isVar($string)
    {
        return preg_match('/\{{2}\w*\}{2}/', $string) ? true : false;
    }

    private static function isExpression($string)
    {
        if(
            preg_match('/\{{2}\w*\}{2}[\+||\-||\*||\/]\d+(\.\d+)?/', $string) ||
            preg_match('/\d+(\.\d+)?[\+||\-||\*||\/]\{{2}\w*\}{2}/', $string) ||
            preg_match('/(\d+(\.\d+)?[\+||\-||\*||\/])?\{{2}\w*\}{2}[\+||\-||\*||\/]\{{2}\w*\}{2}([\+||\-||\*||\/]\d+(\.\d+)?)?/', $string)
        )
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    private static function calc($string, $product)
    {
        $expression = self::buildExpression($string, $product);

        eval('$result = '.$expression.';');
        $result = number_format($result, 2, ',', '');

        return $result;
    }

    private static function buildExpression($string, $product)
    {
        preg_match_all('/\{{2}(\w+)\}{2}/', $string, $matches);
        $expression = '';

        foreach ($matches[0] as $key => $match)
        {
            $attribute = self::extractAttribute($match);
            $value = number_format(self::getAttributeValue($attribute, $product), 2, '.', '');
            $expression = preg_replace('/(\{{2}\w+\}{2})/', $value, $string);
        }

        return $expression;
    }

    private static function extractAttribute($var)
    {
        return preg_replace('/(.*)?\{{2}(.*)\}{2}(.*)?/', '$2', $var);
    }

    private static function getAttributeValue($attribute, $product)
    {
        switch($attribute)
        {
            case 'price':
                $value = number_format($product->getFinalPrice(), 2, ',', '');
            break;

            case 'special_price':
                $value = number_format($product->getData('special_price'), 2, ',', '');
            break;

            case 'url':
            case 'url_path':
                $value = Mage::getUrl('', array('_secure' => false)).$product->getData('url_path');
            break;

            case 'small_image':
            case 'image':
                try
                {
                    $value = $product->getImageUrl();
                }
                catch(Exception $e)
                {
                    $value = $product->getSmallImageUrl(200,200);
                    continue;
                }
            break;

            case 'stock':
                $stock = (int)Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
                $value = (string)$stock;
            break;

            case 'category_ids':
                $value = "";
                $categoryIds = $product->getCategoryIds();

                foreach ($categoryIds as $key => $id)
                {
                    $key !== 0 ? $value .=  Mage::getModel('catalog/category')->load($id)->getName().' > ' : '';
                }

                $value = preg_replace('/ \> $/', '', $value);
            break;

            case 'description':
                $value = "\"".htmlspecialchars($product->getDescription(), ENT_QUOTES)."\"";
            break;

            case 'meta_title':
                $product->getMetaTitle() ? $value = $product->getMetaTitle() : $value = $product->getName();
            break;

            case 'product_id':
                $value = $product->getId();
            break;

            default:
                $value = self::getAttributeData($attribute, $product);
            break;
        }

        return $value;
    }

    private static function getAttributeData($attribute, $product)
    {
        if(!self::hasMultipleOptions($attribute))
        {
            return $product->getData($attribute);
        }
        else
        {
            return self::getDataFromOptions($attribute, $product);
        }
    }

    private static function hasMultipleOptions($attributeCode)
    {
        $attribute = Mage::getModel('eav/entity_attribute')->loadByCode(4, $attributeCode);
        $frontendType = $attribute->getData('frontend_input');

        switch ($frontendType)
        {
            case 'select':
            case 'multiselect':
                return true;
            break;
            
            default:
                return false;
            break;
        }
    }

    private static function getDataFromOptions($attributeCode, $product)
    {
        $attribute = Mage::getModel('eav/entity_attribute')->loadByCode(4, $attributeCode);
        $allOptions = $attribute->getSource()->getAllOptions(false);
        $id = $product->getData($attributeCode);
        return self::searchID($id, $allOptions);
    }

    private static function searchID($id, $array)
    {
        foreach ($array as $key => $val)
        {
            if($val['value'] == $id)
            {
                return $val['label'];
                break;
            }
        }
    }

    private static function _getLink($path, $filename)
    {
        $baseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        
        return $baseUrl . $path . $filename;
    }

    private static function _getTypeId($type)
    {
        $id = "";

        $type_model = Mage::getModel('sitemaps/type');
        $type_collection = $type_model->getCollection()->addFieldToSelect('type_id')->addFieldToFilter('name', array('eq' => $type))->getFirstItem();

        if(!$type_collection->getId())
        {
            $type_model->setData('name', $type);
            $type_model->save();

            $array = array('mode' => 'set', 'id' => $type_model->getId());
        }
        else
        {
            $array = array('mode' => 'get', 'id' => $type_collection->getId());
        }

        return $array;
    }
}