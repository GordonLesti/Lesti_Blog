<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 13.04.13
 * Time: 11:20
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Block_Adminhtml_Post_Edit_Tab_Comment_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('blogPostEditTabCommentGrid');
        $this->setDefaultSort('comment_id');
        $this->setDefaultDir('ASC');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('blog/post_comment')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('comment_id', array(
            'header'    => Mage::helper('blog')->__('Comment ID'),
            'align'     => 'left',
            'index'     => 'comment_id'
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('blog')->__('Status'),
            'index'     => 'status',
            'type'      => 'options',
            'options'   => Mage::getSingleton('blog/post_comment')->getAvailableStatuses()
        ));

        $this->addColumn('creation_time', array(
            'header'    => Mage::helper('blog')->__('Date Created'),
            'index'     => 'creation_time',
            'type'      => 'datetime',
        ));

        return parent::_prepareColumns();
    }

    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
}