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
     * Save action
     */
    public function saveAction()
    {
        // check if data sent
        if ($data = $this->getRequest()->getPost()) {
            //init model and set data
            $model = Mage::getModel('blog/author');

            $adminUserId = (int) Mage::getSingleton('admin/session')->getUser()->getUserId();

            $model->load($adminUserId, 'admin_user_id');
            $data['admin_user_id'] = $adminUserId;
            $data['author_id'] = $model->getId();
            $model->setData($data);

            Mage::dispatchEvent('blog_author_prepare_save', array('author' => $model, 'request' => $this->getRequest()));


            // try to save it
            try {
                // save the data
                $model->save();

                // display success message
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('blog')->__('The author has been saved.'));
                // clear previously saved data from session
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/index', array('_current'=>true));
                    return;
                }
                // go to grid
                $this->_redirect('*/*/');
                return;

            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addException($e,
                    Mage::helper('blog')->__('An error occurred while saving the author.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/index');
            return;
        }
        $this->_redirect('*/*/');
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