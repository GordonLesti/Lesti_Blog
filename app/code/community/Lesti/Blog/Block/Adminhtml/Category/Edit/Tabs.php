<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 03.04.13
 * Time: 15:31
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Block_Adminhtml_Category_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('category_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('blog')->__('Category Information'));
    }
}