<?php
class Itaro_Sitemaps_Block_Admin_Main_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
 
    public function __construct()
    {
        parent::__construct();
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->setUseAjax(false);
        $this->setId('sitemapsGrid');
        $this->_controller = 'sitemaps';
    }
 
    protected function _prepareCollection()
    {
        $model      = Mage::getModel('sitemaps/sitemap');
        $collection = $model->getCollection();
        $collection->getSelect()->joinLeft(array('types' => 'itaro_sitemap_types'), 'types.type_id = main_table.type', array('types.name'));

        $this->setCollection($collection);
 
        return parent::_prepareCollection();
    }
 
    protected function _prepareColumns()
    {
 
        $this->addColumn('sitemap_id', array(
            'header'        => Mage::helper('sitemaps')->__('ID'),
            'align'         => 'right',
            'width'         => '50px',
            'filter_index'  => 'sitemap_id',
            'index'         => 'sitemap_id',
        ));
 
        $this->addColumn('filename', array(
            'header'        => Mage::helper('sitemaps')->__('Nome do Arquivo'),
            'align'         => 'left',
            'width'         => '150px',
            'filter_index'  => 'filename',
            'index'         => 'filename',
            'type'          => 'text',
            'truncate'      => 50,
            'escape'        => true,
        ));
        
        $this->addColumn('path', array(
            'header'        => Mage::helper('sitemaps')->__('Caminho'),
            'align'         => 'left',
            'width'         => '150px',
            'filter_index'  => 'path',
            'index'         => 'path',
            'type'          => 'text',
            'truncate'      => 50,
            'escape'        => true,
        ));        
        
        $this->addColumn('link', array(
            'header'        => Mage::helper('sitemaps')->__('Link'),
            'align'         => 'left',
            'width'         => '150px',
            'filter_index'  => 'link',
            'index'         => 'link',
            //'type'          => 'input',
            'truncate'      => 50,
            'escape'        => true,
            'renderer' => 'buscapemap/admin_grid_renderer_link'
        ));

        $this->addColumn('name', array(
            'header'        => Mage::helper('sitemaps')->__('Profile'),
            'align'         => 'left',
            'width'         => '150px',
            'filter_index'  => 'name',
            'index'         => 'name',
            'type'          => 'text',
            'truncate'      => 50,
            'escape'        => true,
        ));         
        
        $this->addColumn('created_at', array(
            'header'        => Mage::helper('sitemaps')->__('Criado em'),
            'align'         => 'left',
            'width'         => '150px',
            'filter_index'  => 'dt.created_at',
            'index'         => 'created_at',
            'type'          => 'datetime',
            'truncate'      => 50,
            'escape'        => true,
        ));

        $this->addColumn('updated_at', array(
            'header'        => Mage::helper('sitemaps')->__('Atualizado em'),
            'align'         => 'left',
            'width'         => '150px',
            'filter_index'  => 'dt.updated_at',
            'index'         => 'updated_at',
            'type'          => 'datetime',
            'truncate'      => 50,
            'escape'        => true,
        ));
                
        $this->addColumn('action',
            array(
                'header'    => Mage::helper('sitemaps')->__('Ação'),
                'width'     => '150px',
                'type'      => 'action',
                'getter'	=> 'getSitemapId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('sitemaps')->__('Editar'),
                        'url'     => array(
                        'base'    =>'*/*/edit'
                         ),
                         'field'   => 'id'
                    ),
                    array(
                        'caption' => Mage::helper('sitemaps')->__('Atualizar XML'),
                        'url'     => array(
                            'base'=>'*/*/update'
                         ),
                         'field'   => 'id'
                    ),
                    array(
                        'caption' => Mage::helper('sitemaps')->__('Apagar'),
                        'url'     => array(
                            'base'=>'*/*/delete'
                         ),
                         'field'   => 'id'
                    ),
                ),
                'filter'    => false,
                'sortable'  => false
        ));
 
        return parent::_prepareColumns();
    }
}