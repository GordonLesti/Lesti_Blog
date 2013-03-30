<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 30.03.13
 * Time: 21:56
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Block_Adminhtml_Category extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    /**
     * Block constructor
     */
    public function __construct()
    {
        $this->_controller = 'adminhtml_category';
        $this->_blockGroup = 'blog';
        $this->_headerText = Mage::helper('blog')->__('Manage Categories');

        parent::__construct();

        if ($this->_isAllowedAction('save')) {
            $this->_updateButton('add', 'label', Mage::helper('blog')->__('Add New Category'));
        } else {
            $this->_removeButton('add');
        }

    }

    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('blog/category/' . $action);
    }

}