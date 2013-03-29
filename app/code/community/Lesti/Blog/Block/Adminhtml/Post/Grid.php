<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 28.03.13
 * Time: 14:42
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Block_Adminhtml_Post_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('blogPostGrid');
        $this->setDefaultSort('identifier');
        $this->setDefaultDir('ASC');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('blog/post')->getCollection()
            ->addAuthorToResult();
        $collection->setFirstStoreFlag(true);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $baseUrl = $this->getUrl();

        $this->addColumn('title', array(
            'header'    => Mage::helper('blog')->__('Title'),
            'align'     => 'left',
            'index'     => 'title',
        ));

        $this->addColumn('identifier', array(
            'header'    => Mage::helper('blog')->__('URL Key'),
            'align'     => 'left',
            'index'     => 'identifier'
        ));

        $this->addColumn('author', array(
            'header'    => Mage::helper('blog')->__('Author'),
            'align'     => 'left',
            'index'     => 'username'
        ));

        /**
         * Check is single store mode
         */
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'        => Mage::helper('blog')->__('Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback'
                => array($this, '_filterStoreCondition'),
            ));
        }

        $this->addColumn('is_active', array(
            'header'    => Mage::helper('blog')->__('Status'),
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => Mage::getSingleton('blog/post')->getAvailableStatuses()
        ));

        $this->addColumn('creation_time', array(
            'header'    => Mage::helper('blog')->__('Date Created'),
            'index'     => 'creation_time',
            'type'      => 'datetime',
        ));

        $this->addColumn('update_time', array(
            'header'    => Mage::helper('blog')->__('Last Modified'),
            'index'     => 'update_time',
            'type'      => 'datetime',
        ));

//        $this->addColumn('page_actions', array(
//            'header'    => Mage::helper('blog')->__('Action'),
//            'width'     => 10,
//            'sortable'  => false,
//            'filter'    => false,
//            'renderer'  => 'adminhtml/cms_page_grid_renderer_action',
//        ));

        return parent::_prepareColumns();
    }

    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }

    /**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('post_id' => $row->getId()));
    }

}