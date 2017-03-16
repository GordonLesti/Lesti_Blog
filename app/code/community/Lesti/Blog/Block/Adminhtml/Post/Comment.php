<?php

class Lesti_Blog_Block_Adminhtml_Post_Comment extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Block constructor
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_post_comment';
        $this->_blockGroup = 'blog';
        $this->_headerText = Mage::helper('blog')->__('Manage Comments');

        parent::__construct();
        $this->_removeButton('add');
    }

}
