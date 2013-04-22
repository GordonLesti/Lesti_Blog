<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gordon
 * Date: 22.04.13
 * Time: 22:27
 * To change this template use File | Settings | File Templates.
 */
class Lesti_Blog_Adminhtml_Blog_AuthorController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init actions
     *
     * @return Lesti_Blog_Adminhtml_Blog_AuthorController
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        $this->loadLayout()
            ->_setActiveMenu('blog/post')
            ->_addBreadcrumb(Mage::helper('blog')->__('Blog'), Mage::helper('blog')->__('Author'))
            ->_addBreadcrumb(Mage::helper('blog')->__('Author'), Mage::helper('blog')->__('Author'))
        ;
        return $this;
    }


    public function indexAction()
    {
        $this->_title($this->__('Blog'))
            ->_title($this->__('Author'));

        // 1. Get ID and create model
        $id = (int) Mage::getSingleton('admin/session')->getUser()->getUserId();

        // 2. Initial checking
        if ($id) {
            $model = Mage::getModel('blog/author')->load($id, 'admin_user_id');
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('blog')->__('This author no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getTitle() : $this->__('Author'));

        // 3. Set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        // 4. Register model to use later in blocks
        Mage::register('blog_author', $model);

        // 5. Build edit form
        $this->_initAction()
            ->_addBreadcrumb(
                $id ? Mage::helper('blog')->__('Edit Author')
                    : Mage::helper('blog')->__('New Author'),
                $id ? Mage::helper('blog')->__('Edit Author')
                    : Mage::helper('blog')->__('New Author'));

        $this->renderLayout();
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('blog/author');
    }

}