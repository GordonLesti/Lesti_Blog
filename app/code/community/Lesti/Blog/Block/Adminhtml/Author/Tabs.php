<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 22.04.13
 * Time: 22:47
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Block_Adminhtml_Author_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('author_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('blog')->__('Author Information'));
    }
}