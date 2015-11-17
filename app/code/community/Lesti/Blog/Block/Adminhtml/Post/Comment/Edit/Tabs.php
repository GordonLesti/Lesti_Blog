<?php

class Lesti_Blog_Block_Adminhtml_Post_Comment_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('post_comment_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('blog')->__('Comment Information'));
    }
}
