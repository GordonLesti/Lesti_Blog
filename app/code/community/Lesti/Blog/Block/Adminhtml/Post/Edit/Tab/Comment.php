<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 13.04.13
 * Time: 11:14
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Block_Adminhtml_Post_Edit_Tab_Comment
    extends Mage_Adminhtml_Block_Widget_Grid_Container
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Block constructor
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_post_edit_tab_comment';
        $this->_blockGroup = 'blog';
        $this->_headerText = Mage::helper('blog')->__('Manage Comments');

        parent::__construct();
        $this->_removeButton('add');
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('blog')->__('Post Comments');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('blog')->__('Post Comments');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

}