<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 29.03.13
 * Time: 12:34
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Block_Adminhtml_Post_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('post_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('blog')->__('Post Information'));
    }
}