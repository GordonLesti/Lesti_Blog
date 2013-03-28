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

    /**
     * Create new post
     */
    public function newAction()
    {
        // the same form is used to create and edit
        $this->_forward('edit');
    }

    /**
     * Edit post
     */
    public function editAction()
    {
        $this->_title($this->__('Blog'))
            ->_title($this->__('Post'))
            ->_title($this->__('Manage Posts'));

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('post_id');
        $model = Mage::getModel('blog/post');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('blog')->__('This post no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getTitle() : $this->__('New Post'));

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('blog_post', $model);

        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb(
            $id ? Mage::helper('blog')->__('Edit Post')
                : Mage::helper('blog')->__('New Post'),
            $id ? Mage::helper('blog')->__('Edit Post')
                : Mage::helper('blog')->__('New Post'));

        $this->renderLayout();
    }

}