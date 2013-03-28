<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 28.03.13
 * Time: 14:30
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Adminhtml_Blog_PostController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Init actions
     *
     * @return Lesti_Blog_Adminhtml_Blog_PostController
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('blog/post')
            ->_addBreadcrumb(Mage::helper('blog')->__('Blog'), Mage::helper('blog')->__('Post'))
            ->_addBreadcrumb(Mage::helper('blog')->__('Manage Posts'), Mage::helper('blog')->__('Manage Posts'))
        ;
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Blog'))
            ->_title($this->__('Posts'))
            ->_title($this->__('Manage Posts'));

        $this->_initAction();
        $this->renderLayout();
    }

}