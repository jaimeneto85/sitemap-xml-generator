<?php
class Sitemaps_SitemapsController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
    	$this->loadLayout()
            ->_addContent($this->getLayout()->createBlock('sitemaps/admin_main'))
            ->renderLayout();    		    	
    }
 
    public function newAction()
    {
        $this->loadLayout()
        ->_addContent($this->getLayout()->createBlock('sitemaps/admin_new'))
        ->renderLayout();
    }

    public function editAction()
    {
        $this->loadLayout();
        
        $this->_addContent($this->getLayout()->createBlock('sitemaps/admin_edit'));
        
        $this->renderLayout();
    }

    public function saveAction()
    {
        $params = $this->getRequest()->getParams();
        
        if(!$params)
        {
            Mage::getSingleton('adminhtml/session')->addNotice($this->__('Nenhum parâmetro especificado'));
            $this->getResponse()->setRedirect($this->getUrl('*/*/'));
        }
        else
        {
            if(Mage::getModel('sitemaps/sitemap')->genSitemap($params, 'edit'))
            {
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sitemaps')->__('XML atualizado com sucesso.'));
                $this->getResponse()->setRedirect($this->getUrl('*/*/'));
            }
            else
            {
                Mage::getSingleton('adminhtml/session')->addNotice($this->__('Erro ao gerar arquivo.'));
                $this->getResponse()->setRedirect($this->getUrl('*/*/'));
            }
        }
    }

    public function updateAction()
    {
        $sitemapId = $this->getRequest()->getParam('id', false);

        $sitemap = Mage::getModel('sitemaps/sitemap')->load($sitemapId);

        $type = Mage::getModel('sitemaps/type')->load($sitemap->getType());
        $fields = json_decode($type->getFields());

        $params['sitemap_id'] = $sitemapId;
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

        if(Mage::getModel('sitemaps/sitemap')->genSitemap($params, 'edit'))
        {
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sitemaps')->__('XML atualizado com sucesso.'));
            $this->getResponse()->setRedirect($this->getUrl('*/*/'));
        }
        else
        {
            Mage::getSingleton('adminhtml/session')->addNotice($this->__('Erro ao gerar arquivo.'));
            $this->getResponse()->setRedirect($this->getUrl('*/*/'));
        }
    }
 
    public function genAction()
    {
        $params = $this->getRequest()->getParams();
        
        if(!$params)
        {
            Mage::getSingleton('adminhtml/session')->addNotice($this->__('Nenhum parâmetro especificado'));
            $this->getResponse()->setRedirect($this->getUrl('*/*/'));
        }
        else
        {
            if(Mage::getModel('sitemaps/sitemap')->genSitemap($params))
            {
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sitemaps')->__('XML gerado com sucesso.'));
                $this->getResponse()->setRedirect($this->getUrl('*/*/'));
            }
            else
            {
                Mage::getSingleton('adminhtml/session')->addNotice($this->__('Erro ao gerar arquivo.'));
                $this->getResponse()->setRedirect($this->getUrl('*/*/'));
            }
        }
    }

    public function deleteAction()
    {       
        $sitemapId = $this->getRequest()->getParam('id', false);

        try {
            
            $sitemap = Mage::getModel('sitemaps/sitemap')->load($sitemapId);
            
            $data = $sitemap->getData();

            $io = new Varien_Io_File();
            
            $io->open(array('path' => Mage::getBaseDir().'/'.$data["path"]));
            
            if($io->fileExists($data["filename"]))
            {
                $io->rm($data["filename"]);
            }
            
            $io->close();
            
            Mage::getModel('sitemaps/sitemap')->setId($sitemapId)->delete();
           
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sitemaps')->__('XML deletado com sucesso.'));
            
            $this->getResponse()->setRedirect($this->getUrl('*/*/'));
            
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }

    }
}