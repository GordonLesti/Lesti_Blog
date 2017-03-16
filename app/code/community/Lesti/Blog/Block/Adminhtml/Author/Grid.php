<?php

class Lesti_Blog_Block_Adminhtml_Author_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('blogAuthorGrid');
        $this->setDefaultSort('author_name');
        $this->setDefaultDir('ASC');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('blog/author')->getCollection();
        $collection->addAdminUserToResult();
        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('author_id', array(
            'header'    => Mage::helper('blog')->__('ID'),
            'align'     => 'left',
            'index'     => 'author_id',
            'width'     => '50px'
        ));
        
        $this->addColumn('author_name', array(
            'header'    => Mage::helper('blog')->__('Name'),
            'align'     => 'left',
            'index'     => 'author_name',
        ));
        
        $this->addColumn('admin_user_username', array(
            'header'    => Mage::helper('blog')->__('Admin User'),
            'align'     => 'left',
            'index'     => 'admin_user_username',
        ));
        
        return parent::_prepareColumns();
    }
    
    /**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('author_id' => $row->getId()));
    }

}
