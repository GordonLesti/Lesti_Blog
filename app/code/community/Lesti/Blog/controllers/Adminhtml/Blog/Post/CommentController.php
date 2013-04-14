<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 14.04.13
 * Time: 16:02
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Adminhtml_Blog_Post_CommentController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Init actions
     *
     * @return Lesti_Blog_Adminhtml_Blog_Post_CommentController
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('blog/comment')
            ->_addBreadcrumb(Mage::helper('blog')->__('Comment'), Mage::helper('blog')->__('Comment'))
            ->_addBreadcrumb(Mage::helper('blog')->__('Manage Comments'), Mage::helper('blog')->__('Manage Comments'))
        ;
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Blog'))
            ->_title($this->__('Comments'))
            ->_title($this->__('Manage Comments'));

        $this->_initAction();
        $this->renderLayout();
    }

}