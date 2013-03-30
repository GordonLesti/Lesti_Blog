<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 30.03.13
 * Time: 21:53
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Adminhtml_Blog_CategoryController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init actions
     *
     * @return Lesti_Blog_Adminhtml_Blog_CategoryController
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('blog/category')
            ->_addBreadcrumb(Mage::helper('blog')->__('Blog'), Mage::helper('blog')->__('Category'))
            ->_addBreadcrumb(Mage::helper('blog')->__('Manage Categories'), Mage::helper('blog')->__('Manage Categories'))
        ;
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Blog'))
            ->_title($this->__('Categories'))
            ->_title($this->__('Manage Categories'));

        $this->_initAction();
        $this->renderLayout();
    }
}